Victoire DCMS Search Bundle
============

##What is the purpose of this bundle

This bundles gives you access to the *Search Widget* which is a search bar that can find any text.

It works with elastica and searches automatically in every searchable widget (thanks to the widget's embedded configuration).
You place an emitter spot (i.e : the search bar) and define the result page where you'll place a receiver spot.

##Specific search within BusinessEntities

If you want to search your BusinessEntities, you'll have to define your own configuration for your BusinessEntities:

```yml
fos_elastica:
    clients:
        default: { host: 127.0.0.1, port: 9200 }
    serializer:
        callback_class: FOS\ElasticaBundle\Serializer\Callback
        serializer: serializer
    indexes:
        business: #business is important for now, you need to use this name otherwhise search-widget won't search here
            types:
                Jedi:
                    serializer:
                        groups: [search]
                    mappings:
                        title: ~
                        city: ~
                        description: ~
                        contractType: ~
                        subtitle: ~
                    persistence:
                        driver: orm
                        model:  Acme\DemoBundle\Entity\Jedi
                        provider: ~
                        listener: ~
                        finder: ~
```


##Set Up Victoire

If you haven't already, you can follow the steps to set up Victoire *[here](https://github.com/Victoire/victoire/blob/master/setup.md)*

##Install the bundle

Run the following composer command :

    php composer.phar require friendsofvictoire/search-widget

###Reminder

Do not forget to add the bundle in your AppKernel !

```php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Victoire\Widget\SearchBundle\VictoireWidgetSearchBundle(),
            );

            return $bundles;
        }
    }
```
