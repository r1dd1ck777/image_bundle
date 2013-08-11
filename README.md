RidImageBundle (beta)
==================================
Provides handling uploaded files and thumbnail creation, clean templates and flexible configuration.

### Full documentation soon...

### How it works
1. Store file name in database in a string
2. Replace PHP field representation from string to RidImage
3. Automatically inject configuration to RidImage
4. Automatically handle uploaded files according to configuration
5. Maximum parameters in config file (not in entities, services or templates)

### Twig Example:
Config:
``` yaml
rid_image:
    presets:
        user_avatars:
            path: uploads/user/avatars/
            default: image/default-user-avatar.png
            thumbnails:
                medium:
                    default: image/default-user-avatar-90.png
                    width: 90
                    height: 90
                    type: inset
                tiny:
                    default: image/default-user-avatar-30.png
                    width: 30
                    height: 20
                    type: outbound
    fields:
        Some\Bundle\Entity\User:
            avatar: user_avatars
```

Twig:
``` php
    // simply find user and then:
    {{ asset(user.avatar) }}          // /uploads/user/avatars/random_name.jpg (full size)
    {{ asset(category.image.small) }} // /uploads/user/avatars/small_random_name.jpg (90x90)
    {{ asset(category.image.tiny) }}  // /uploads/user/avatars/tiny_random_name.jpg (30x20)

    // if image field is empty:
    {{ asset(user.avatar) }}          // /image/default-user-avatar.png
    {{ asset(category.image.small) }} // /image/default-user-avatar-90.png
    {{ asset(category.image.tiny) }}  // /image/default-user-avatar-30.png

```

### Instalation
1. Composer: "rid/image-bundle": "dev-master",
2. AppKernel: new Rid\Bundle\ImageBundle\RidImageBundle(),
3. Config.yml:

``` yaml
doctrine:
    dbal:
        types:
            rid_image: Rid\Bundle\ImageBundle\DBAL\Types\RidImageType
            rid_file: Rid\Bundle\ImageBundle\DBAL\Types\RidFileType
        mapping_types:
            rid_image: rid_image
            rid_file: rid_file

rid_image:
    presets:
        # here you can create as many presets as you need
        somePresetName:
            path: uploads/category/
            thumbnails:
                small: # define eny name for thumbnails
                    width: 120
                    height: 70
                # add more thumbnails
    fields:
        Some\Bundle\Entity\Category:
            fieldName: somePresetName
            # add more fields
        # add more classes
```

4. In Your entity class:
``` php
<?php

namespace Some\Bundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Rid\Bundle\ImageBundle\Model\RidImage;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Category
{
    /**
     * @ORM\Column(type="rid_image", length=255, options={"default" = ""})
     */
    protected $image;

    public function __construct()
    {
        $this->image = new RidImage();
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }
}
```

5. Set field type in form builder
``` php
    ->add('image', 'rid_image')
```

### Also implemented:
- Manual handling of uploaded files
- Events

### In future:
- frontend widgets (jcrop)
- grabbing from url
- more tests