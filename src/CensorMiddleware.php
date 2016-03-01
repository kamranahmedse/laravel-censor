<?php namespace KamranAhmed\LaravelCensor;

use Closure;
use Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CensorMiddleware
{
    protected $replaceDict;
    protected $redactDict;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $ability
     * @param string|null              $boundModelName
     *
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next, $ability = null, $boundModelName = null)
    {
        $response = $next($request);

        $this->prepareDictionary();

        $content = $response->getContent();
        $content = $this->censorResponse($content);

        $response->setContent($content);

        return $response;
    }

    private function prepareDictionary()
    {
        $replaceDict = Config::get('censor.replace', []);
        $redactDict  = Config::get('censor.redact', []);

        $replaceDictKeys   = array_keys($replaceDict);
        $replaceDictValues = array_values($replaceDict);

        $replaceDictKeys = $this->normalizeRegex($replaceDictKeys);

        $this->replaceDict = array_combine($replaceDictKeys, $replaceDictValues);
        $this->redactDict  = $this->normalizeRegex($redactDict);
    }

    private function normalizeRegex($dictionary)
    {
        foreach ($dictionary as &$pattern) {
            $pattern = str_replace('%', '(?:[^<\s]*)', $pattern);
        }

        return $dictionary;
    }

    /**
     * Censor the request response.
     *
     * @param $source
     *
     * @return mixed
     */
    protected function censorResponse($source)
    {
        $replaceables = array_keys($this->replaceDict);
        $replaceables = array_merge($replaceables, $this->redactDict);

        // Word boundary and word matching regex
        $replaceables = '\b' . implode('\b|\b', $replaceables) . '\b';
        $regex        = '/>(?:[^<]*?(' . $replaceables . ')[^<]*?)</i';

        // Make the keys lower case so that it is easy to lookup
        // the replacements
        $toReplace = array_change_key_case($this->replaceDict, CASE_LOWER);
        $toRedact  = $this->redactDict;

        $source = preg_replace_callback($regex, function ($match) use ($toReplace, $toRedact) {

            $temp = strtolower($match[1]);

            // If we have to replace it
            if (isset($toReplace[$temp])) {
                return str_replace($match[1], $toReplace[$temp], $match[0]);
            } elseif ($regexKey = $this->getReplaceRegexKey($temp)) {
                return str_replace($match[1], $toReplace[$regexKey], $match[0]);
            } elseif ($this->_inArray($temp, $toRedact) || $this->getRedactRegexKey($temp)) {
                $replaceWith = str_repeat('*', strlen($temp));

                return str_replace($match[1], $replaceWith, $match[0]);
            } else {
                return $match[0];
            }

        }, $source);

        return $source;
    }

    public function getReplaceRegexKey($matched)
    {
        foreach ($this->replaceDict as $pattern => $replaceWith) {
            if (preg_match('/' . $pattern . '/', $matched)) {
                return $pattern;
            }
        }

        return false;
    }

    private function _inArray($needle, $haystack)
    {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }

    public function getRedactRegexKey($matched)
    {
        foreach ($this->redactDict as $pattern) {
            if (preg_match('/' . $pattern . '/', $matched)) {
                return $pattern;
            }
        }

        return false;
    }
}
