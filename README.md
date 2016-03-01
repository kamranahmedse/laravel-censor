# Laravel Censor

> Let's you easily redact or replace the occurences of `words`, `sentences` or `%wildcards%`  

A laravel middleware that will automatically censor the words that you will specify. All you have to do is specify the things, that you want to redact or replace, in a configuration file and these words will automatically be redacted/replaced from the views on whose route you will specify the middleware.

## Installation
Perform the following operations in order to use this middleware
- Run `composer require kamranahmedse/laravel-censor` in your terminal
- **Add Service Provider** 
   Open `config/app.php` and add `KamranAhmed\LaravelCensor\LaravelCensorServiceProvider::class` to the end of `providers` array:

    ```
    'providers' => array(
        ....
        KamranAhmed\LaravelCensor\LaravelCensorServiceProvider::class,
    ),
    ```

- **Register the Middleware** After that open the file `app/Http/Kernel.php` and add the following 

   ```
  'censor' => \KamranAhmed\LaravelCensor\CensorMiddleware::class
   ```

   to the end of `$routeMiddleware` array

   ```
    protected $routeMiddleware = [
        ...
        'censor' => \KamranAhmed\LaravelCensor\CensorMiddleware::class
    ];
   ```

- **Publish Configuration** Open terminal and run

    ```shell
    php artisan vendor:publish
    ```
    
## How to use

- After following the above steps, there will be a `censor.php` file inside the `config` directory. The file has two arrays, namely `replace` and `redact`.
- You have to specify the words that you want to replace in the `replace` array with words or wildcards set to the keys of array and replacements as values i.e.

    ```php
    'replace' => [
	    'idiot'    => '(not a nice word)',
	    'hate%'    => 'peace',      // Wildcard: Will replace the words beginning at hate e.g. hatered, hate, hated etc
	    '%eograph%' =>  'some-graphy-word',         // Willcard: Will replace words containing `eograph` anywhere in the middle e.g. geographic, angeography etc
	    'seventh'  => '7th',
	    'monthly'  => 'every month',
	    'yearly'   => 'every year',
	    'weekly'   => 'every week',
    ],
    ```

- For any words that you want to `redact` or completely remove, you have to specify them in the `redact` array

    ```php
    'redact' => [
       'idiot%',        // e.g. idiot will be replaced with 5 asterisks, idiotic with 7 asterisks etc 
       'password',      // Will be replaced with 8 asterisks
       'word-that-i-really-dislike',
    ],
    ```
   The words specified in `redact` array will turn into asterisks. For example `idiot` will be turned into 5 asterisks (*****).

- Now for any route from which you want these words to be redacted or replaced, place the middleware `censor` over it and it will automatically redact/replace those words from all of the page. For example, below is how you can specify it over the route e.g.
   ```php
   Route::get('post-detail', ['middleware' => 'censor', 'uses' => 'PostController@detail', 'as' => 'postDetail']);
   ```
   Or specify it over the route group so that it may handle all the routes in that group e.g.
    ```php
    Route::group(['prefix' => 'post', 'middleware' => 'censor'], function () {
	    Route::get('detail', ['uses' => 'PostController@detail']);
	    Route::get('add', ['uses' => 'PostController@add']);
    });
    ```
    
## How to Contribute
- Feel free to add some new functionality, improve some existing functionality etc and open up a pull request explaining what you did.
- Report any issues in the [issues section](https://github.com/kamranahmedse/laravel-censor/issues)
- Also you can reach me directly at kamranahmed.se@gmail.com with any feedback
