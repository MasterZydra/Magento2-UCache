# Magento2 Universal Cache

In Magento2 there are a lot of caches built into the system (configuration, layouts, page cache, etc).  
But what if you want to cache a string? Which cache can you use? This package could be the answer.

**Content**  
- [Usage](#usage)
- [Installation](#installation)
- [Updating to latest version](#updating-to-latest-version)

## Usage
The values are stored in a table in the database. The getter and setter will serialize/deserialize the given value.

```PHP
class Demo {
    public function __construct(
        private \MasterZydra\UCache\Helper\UCache $ucache,
    ) { }

    public function doWork()
    {
        // Add a value to the cache
        $this->ucache->save('myModule_cacheKey', ['some' => 'array']);
        // Load a cache key
        $value = $this->ucache->load('myModule_cacheKey');
        // Remove a single cache entry
        $this->ucache->remove('myModule_cacheKey');
        // Flush the entire UCache
        $this->ucache->clean();

        // Cache value for 30 seconds
        $this->ucache->remember('myModule_cacheKey', 30, function () { return 42; });
        // Cache value forever
        $this->ucache->rememberForever('myModule_cacheKey', function () { return 42; });

        // "remember" and "rememberForever" can also be used with functions with arguments
        // The values to pass when the function is called are passed as an array.
        $value = $this->ucache->remember(
            'customerGroupColl',
            10,
            function (CollectionFactory $collFactory, LoggerInterface $logger) {
                $logger->error('remember customer groups');
                return $collFactory->create();
            },
            [$this->collFactory, $this->logger]
        );
    }
}
```

## Installation
This Magento2 module can be installed using composer:  
`> composer require masterzydra/magento2-ucache`

To remove it from the list of required packages use the following command:  
`> composer remove masterzydra/magento2-ucache`

## Updating to latest version
With the following command composer checks all packages in the composer.json for the latest version:  
`> composer update`

If you only want to check this package for newer versions, you can use  
`> composer update masterzydra/magento2-ucache`
