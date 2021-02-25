# Feed Generator

This library aims to simplify compliant feed generation for ShoppingFeed services. If we want to unpack this statement : 
- a *feed* is a file that contains products (a collection of references, descriptions, prices, quantities, image links etc.)
- a *compliant feed* is, by default, an XML file that is compliant with [the rules defined here](https://github.com/shoppingflux/feed-xml/blob/develop/feed.xsd) (for an example of what such a file might look like, [you can check this example](https://github.com/shoppingflux/feed-xml/blob/develop/examples/minimal.xml)); I say by default, because we can have other formats, but more on that later
- you can find out more about the *ShoppingFeed services* [on our website](https://www.shopping-feed.com/)

It allows you to format, filter, map & validate your products using a series of methods defined in this library. Once all this is done, the generator outputs a file: it is *this* file that will be used by the ShoppingFeed solution to import all your products so that they can be dispatched to the different marketplaces. 

When should you use this feed generator ?
- if you are considering using the [ShoppingFeed solution](https://www.shopping-feed.com/)
- and your feed (the file containing all your products) is not in a format that is supported out of the box
- and you wish to have an easy / speedy set-up
- or if you want to provide an out-of-the-box format for new versions of existing e-commerce platforms (such as Magento, Prestashop etc.)
- or if you want to avoid generating and formatting the feed yourself

The generated feed produced by this library is, by default, in [the standard XML ShoppingFeed format](https://github.com/shoppingflux/feed-xml). But the library also allows you to : 
- either choose a different format (for now we only support CSV, on top of the default XML)
- either write your own FormatWriter, by extending the ProductFeedWriterInterface

### Requirements

- PHP version 5.5 or above
- PHP XML extension when using XML output

### Installation

`composer require shoppingfeed/php-feed-generator`

### Overview

The component acts as pipeline to convert any data set to compliant XML output, to a file or any other destination.

From the user's point of view, it consists of mapping your data to a `ShoppingFeed\Feed\Product\Product` object.
The library take cares of the rest : formatting the data and writing the valid XML.


### Getting Started

```php
<?php
namespace ShoppingFeed\Feed;

# first create an instance of the generator
$generator = new ProductGenerator();

# then, you need to define at least one mapper 
$generator->addMapper(
    # more details below
);

# you also need your data / products at hand
$items = [
    [
        # product 1 
    ],
    [
        # product 2
    ],
    # ...
];

# finally, you can generate the feed
$generator->write($items);
```

This is a skeleton example. In the following sections, we will detail each step, specifying different options and adding more intermediary optional steps.

### Mandatory fields

In the generated feed, only valid products will be written. A minimal valid Product item requires the following properties:

- A `reference` (aka SKU)
- A `name`
- A `price`

Your data source must contain this information, or you can hardcode some of it during the feed generation.

### Recommended fields

Even if they are not required, the following fields will ensure that your product will not be refused by any channel :

#### link

back-link to the product on your shop, should be present for shopbot (Google Shopping...etc)

#### image

At least one product image will prevent your product from being refused.

#### description

Push a short description. Embedded html / javascript / css is irrelevant for a vast majority of channels.

#### quantity

When not set, the quantity is set to zero. So make sure to set this field and start selling !

#### shipping cost / shipping time

Shipping information is very useful for the final client and an effective total cart calculation. If they apply, you should provide them.

#### attributes

Essential attributes (when apply) are color and size, but in general always provides attributes for products and their variations.  

### Specify where the feed will be written

By default, XML is written to the standard output, but you can specify an uri, like a local file :

```php
<?php
namespace ShoppingFeed\Feed; 

$generator = new ProductGenerator();
$generator->setUri('file://my-feed.xml');
```

### Compress output

From our experience, feed upload / download accounts for a large chunk of time in the import process at ShoppingFeed.
        
As XML may be considered a "heavy" format, it has a non negligible impact on network performance and cost : 
That is why we **highly recommend compression** when generating feeds in production.

Compression is natively supported by [PHP stream wrappers](http://php.net/manual/en/wrappers.compression.php), so the only thing you have to do is to specify the compression stream in the file uri 
(ShoppingFeed only supports *gzip* compression at the moment) :

```php
<?php
namespace ShoppingFeed\Feed;
 
$generator = new ProductGenerator();
$generator->setUri('compress.zlib://my-feed.xml.gz');
```

It will reduce the final file size to approximately x9 smaller. Zlib compression has a very low footprint on the processor.

### Set up information

The generator accepts some settings or useful information, like the platform or application for which the feed has been generated.
We recommend to always specify this setting, because it can help us to debug and provide appropriate support.

```php
<?php
namespace ShoppingFeed\Feed;

$generator = (new ProductGenerator)
    ->setUri('file://my-feed.xml')
    ->setPlatform('Magento', '2.2.1');

# Set any extra vendor attributes.
$generator    
    ->setAttribute('storeName', 'my great store')
    ->setAttribute('storeUrl', 'http://my-greate-store.com');
```

### Define output format

Currently, the library supports the following format:

- `xml` : default, all features available
- `csv` : no support for feed attributes, platform and metadata. Shipping and discount are limited to the 1 item.

The format can be defined like this

```php
<?php
namespace ShoppingFeed\Feed;
# constructor
$generator = new ProductGenerator('file://my-feed.csv', 'csv');
# Or with setter
$generator->setWriter('csv');
````  

#### CSV Specific options

The CSV output writer requires storing data temporarily. By default, data is flushed to a file once 2MB of memory is reached.
You can disable or increase memory usage like this :

```php
<?php
namespace ShoppingFeed\Feed;
# Disable memory allocation
Csv\CsvProductFeedWriter::setDefaultMaxMemoryUsage(0);

# Allocate 100MB of memory (value is in bytes)
Csv\CsvProductFeedWriter::setDefaultMaxMemoryUsage(100^4);

# No memory limit
Csv\CsvProductFeedWriter::setDefaultMaxMemoryUsage(-1);
```

### Basic Example

Once the feed instance is properly configured, you must provide at least one `mapper` and a dataset to run the feed generation against.

```php
<?php
namespace ShoppingFeed\Feed;

$generator = (new ProductGenerator)->setPlatform('Magento', '2.2.1');

# Mappers are responsible for converting your data format to populated product
$generator->addMapper(function(array $item, Product\Product $product) {
    $product
        ->setName($item['title'])
        ->setReference($item['sku'])
        ->setPrice($item['price'])
        ->setQuantity($item['quantity'])
	->setAttribute('custom1', $item['custom1'])
	->setAttribute('custom2', $item['custom2']);
});

# Data set fixtures
$items[0] = ['sku' => 1, 'title' => 'Product 1', 'price' => 5.99, 'quantity' => 3];
$items[1] = ['sku' => 2, 'title' => 'Product 2', 'price' => 12.99, 'quantity' => 6];

# now generates the feed with $items collection
$generator->write($items);
```
That's all ! Put this code in a script then run it, XML should appear to your output (browser or terminal).

If the number of items / products is sufficiently large to cause a memory issues, you can always use [a generator](https://www.php.net/manual/en/language.generators.overview.php).

```php
function getLotsOfProducts(): iterable
{
    while($batchOfProducts = getNextTenProductsFromTheDatabse()) {
        foreach ($batchOfProducts as $product) {
            yield $product;
        }
    }
}

$generator->write(getLotsOfProducts());
```

## Data Processing pipeline

This schema describes the execution pipeline loop that the generator will apply when generating your feed, for each product :

```
-> exec processors[] -> exec filters[] -> exec mappers[] -> validate -> write ->
|                                                                              |
<------------------------------------------------------------------------------
```

### Processors

In some case, you may need to pre-process data before mapping it.

This can also be achieved in mappers or in your dataset, but sometimes things have to be separated, so you can register processors that are executed before mappers, and prepare your data before the mapping process.

In this example, we harcode the description when none is provided.

```php
<?php
namespace ShoppingFeed\Feed;

$generator = new ProductGenerator();

$generator->addProcessor(function(array $item) {
    if (! isset($item['description'])) {
        $item['description'] = 'Product description coming soon';
    }
    # modified data must be returned
    return $item;
});
 
$generator->addMapper(function(array $item, Product\Product $product) {
    $product->setDescription($item['description']);
});
```

You can register as many processors as you want, but processors :

- Only accept $item as argument
- Expect a return value
- The returned value is used for the next stage (next processor or next mapper)


### Filters

Filters, as the name implies, are designed to discard some items from the feed, while keeping others.

Filters are executed **after** processors, because items must be completely filled in before making the decision to keep it or not.

The expected return value of a filter is a boolean, where:

- `TRUE`  : the item is passed to the next item in the pipeline, namely mappers
- `FALSE` : the item is discarded


```php
<?php
namespace ShoppingFeed\Feed;

$generator = new ProductGenerator();

# Ignore all items with undefined quantity
$generator->addFilter(function(array $item) {
   return isset($item['quantity']);
});

# Ignore all items with prices above 10
$generator->addFilter(function(array $item) {
   return $item['price'] <= 10;
});

# only items that pass the previous filter conditions are considered by mappers
$generator->addMapper(function(array $item, Product\Product $product) {
    // do some stuff
});
```

### Mappers

As stated above, at least one mapper must be registered, this is where you populate the `Product` instance, which is later converted to XML (or other formats) by the library.

The `addMapper` method accepts any [callable type](http://php.net/manual/en/language.types.callable.php), like functions or invokable objects.

Mappers are invoked on each iteration over the collection you provided in the `generate` method, with the following arguments

- `(mixed $item, ShoppingFeed\Feed\Product\Product $product)`

where:

- `$item` is your data
- `$product` is the object to populate with `$item` data

Note that there is *no expected return value* from your callback

#### How mappers are invoked ?

You can provide as many mappers as you want, they are executed in FIFO (First in, First out) mode - that is to say in the order provided.
The ability to register more than one mapper can help keep your code organized as you want, and there is no particular performance hits when registering multiple mappers.

As an example of organisation, you can register 1 mapper for handling the main product, and 1 mapper for its variations.

```php
<?php
namespace ShoppingFeed\Feed;

$generator = new ProductGenerator();

# Populate product properties
$generator->addMapper(function(array $item, Product\Product $product) {
    $product
        ->setName($item['title'])
        ->setReference($item['sku'])
        ->setPrice($item['price'])
        ->setQuantity($item['quantity']);
});

# Populate product's variations. Product properties are already populated by the previous mapper
$generator->addMapper(function(array $item, Product\Product $product) {
    foreach ($item['variations'] as $item) {
        $variation = $product->createVariation();
        $variation
            ->setReference($item['sku'])
            ->setPrice($item['price'])
            ->setQuantity($item['quantity'])
	    ->setAttribute('custom1', $item['custom1'])
	    ->setAttribute('custom2', $item['custom2']);
    }
});
```

### Validation

By default, the generator does not run validation against products.
When developing, you may catch invalid products and understand why some of them are invalid.
To do so, you can specify how the generator should handle validation :

```php
<?php
namespace ShoppingFeed\Feed;

$generator = new ProductGenerator();

# Only exclude invalid products, with no error reporting.
$generator->setValidationFlags(ProductGenerator::VALIDATE_EXCLUDE);

# Or throw an exception once invalid product is met
$generator->setValidationFlags(ProductGenerator::VALIDATE_EXCEPTION);
```

Validation requires that your products contain at least:

- `reference` (SKU)
- `price`
- `name` (only required for parent's products)


### Customizing the output

The Product generator uses by default an XML writer, but you can register your own `ShoppingFeed\Feed\ProductFeedWriterInterface` implementation if you need to customize the output.

#### Registering a new writer

Writers are stored at class level, so you need to register them only once :

```php
<?php
ShoppingFeed\Feed\ProductGenerator::registerWriter('csv', 'App\CsvWriter');
```

- `csv` : is an arbitrary writer identifier **alias**
- `App\CsvWriter` : class that implements `ShoppingFeed\Feed\ProductFeedWriterInterface

Once this is done, you can specify the writer **alias** as a second parameter of the constructor :

```php
<?php
$generator = new ShoppingFeed\Feed\ProductGenerator('file.csv', 'csv');
```

### Performance Considerations

Generating large XML feeds can be a very long process, so our advice is :

- Run feed generation offline from a command line / cron : PHP [set max_exection_time to 0](http://php.net/manual/en/info.configuration.php#ini.max-execution-time) in this mode
- Try to generate the feed on a different machine than the one acting as your web server, or on a machine where the traffic is limited : the feed generation process may impact the visitors' experience, by blocking a thread  
- Limit the number of SQL requests : avoid running requests in a loop
- Paginate your results on large datasets, this will limit memory consumption and network traffic per request
- Make uses of compression when writing to a file : this will save network bandwidth and we will be able to import your feed faster

Internally, the library uses `XmlReader` / `XmlWriter` to limit memory consumption. Product objects and generated XML are flushed from memory after each iteration.
This guarantees that memory usage will not increase with the number of products to write, but will instead only depend on the "size" of each product.


### Execute test commands

If you just want to play around with the library and don't yet have products of your own, you can use the following command to generate "dummy" products that will allow you to play with the library and get a feel for how it works. 

```bash
php tests/functional/<filename>.php <file> <number-of-products> <number-of-children-per-product>

# example:
php tests/functional/products-random.php feed.xml 1000 0
```   
