<?php namespace KamranAhmed\LaravelCensor;

use Closure;
use Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CensorMiddleware
{
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

        $toReplace = Config::get('censor.replace');
        $toRedact = Config::get('censor.redact');

        $content = $response->getContent();
        $content = $this->censorResponse( $content, $toReplace, $toRedact );

        $response->setContent( $content );

       return $response;
    }

    /**
     * Censor the request response.
     */
    protected function censorResponse($source, $toReplace, $toRedact)
    {
        $replaceables = array_keys( $toReplace );
        $replaceables = array_merge( $replaceables, $toRedact );

        // Word boundary and word matching regex
        $replaceables = '\b' . implode('\b|\b', $replaceables) . '\b';
        $regex = '/>(?:[^<]*?(' . $replaceables . ')[^<]*?)</i';

        // Make the keys lower case so that it is easy to lookup
        // the replacements
        $toReplace = array_change_key_case($toReplace, CASE_LOWER);

        $source = preg_replace_callback($regex, function ( $match ) use ( $toReplace, $toRedact ) {

            $temp = strtolower($match[1]);

            // If we have to replace it
            if ( isset( $toReplace[ $temp ] )) {
                // return $toReplace[ $temp ];
                return str_replace($match[1], $toReplace[ $temp ], $match[0]);
            } else if ( $this->_inArray( $temp, $toRedact ) ) {
                // return str_repeat('*', strlen( $temp ));
                $replaceWith = str_repeat('*', strlen( $temp ));

                return str_replace($match[1], $replaceWith, $match[0]);
            } else {
                return $match[0];
            }

        }, $source);

        return $source;
    }

    private function _inArray($needle, $haystack) {
        return in_array(strtolower($needle), array_map('strtolower', $haystack));
    }
}

?>