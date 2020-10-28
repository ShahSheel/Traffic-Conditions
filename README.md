# Here-Traffic

## About
This package gets the traffic conditions between 2 given `lats` and `lons`. For example Point A is `1234.123, -123,23` and Point B is `5678,34, -2392`. The traffic condition will be returned for current situtation in them coordinates
## Usage

* Add the following to your `repositories` in `composer.json` 


```

* Add the dev-requirement

```javascript

"sheel/here_traffic": "dev-master"
```


* Perform composer require Sheel/here_traffic
* Navigate to `App\Config\App.php` and add the Service Provider  - `Sheel\here_traffic\Providers\TrafficServiceProvider::class`
