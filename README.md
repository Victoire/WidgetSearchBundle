#Victoire CMS Search Bundle

Need to add a search in a victoire cms website ?

First you need to have a valid Symfony2 Victoire edition.
Then you just have to run the following composer command :

    php composer.phar require friendsofvictoire/search-widget

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

The search widget works with elastica and search automatically in every searchable widget (thanks to the widget's embedded configuration).
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
