# Symfony bundle добавляющий новый тип данных для Doctrine ORM – DefineEnum

Тип данных DefineEnum позволяет объявлять enum-типы с произвольным названием и передавать им в качестве значений, значения из констант классов, которые затем можно использовать в качестве типа для Doctrine ORM.

Установка
---------

```bash
composer require nalogka/doctrine-enum-type
```

Пример использования
----------------- 

```php
/**
 * @ORM\Entity()
 */
class User
{
    const ROLE_USER = 'user';
    const ROLE_SUPERVISOR = 'supervisor';
    const ROLE_ADMINISTRATOR = 'administrator';
    
    const AVAILABLE_ROLES = [
        ROLE_USER,
        ROLE_SUPERVISOR,
        ROLE_ADMINISTRATOR,
    ];
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="CUSTOM")
     */
    public $id;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    public $name;
    
    /**
     * @ORM\Column(type="enum_roles")
     * @DefineEnum("enum_roles", values={
     *     User::AVAILABLE_ROLES
     * })
     */
    public $role;
}
```

