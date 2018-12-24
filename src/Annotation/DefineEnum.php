<?php

namespace Nalogka\EnumType\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "CLASS"})
 * @TODO значения в values не подхватываются из БД докриной при создании миграции. Доктрина берет значения из аннотации
 */
class DefineEnum
{
    public $typeName;
    public $values;
}
