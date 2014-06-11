Wordpress Theme Base
=====================

## Get started
### Step1
add following code to your composer.json

    "autoload": {
    	"psr-0": {
    		"": "classes/"
    	}
    },

### Step2
write following code to functions.php which in your WrodPress theme

    require __DIR__ . '/vendor/autoload.php';

    use Jhonyspicy\Wordpress\Theme\Base\Base as ThemeBase;
    ThemeBase::initialize();

### Step3
see example directory.  
it include sample code.  
copy it to WordPress theme directory  
and you can see how it works  
have fun :)

## LICENSE

This is released under the LGPLv3, see LICENSE.  
  
Copyright (c) 2014 jhonyspicy
