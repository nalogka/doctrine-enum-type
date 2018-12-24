<?php

namespace Nalogka\EnumType\DBAL\Types;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Тип данных "перечисление"
 */
class EnumType extends Type
{
    public $typeName;
    public $values;

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     * @throws DBALException
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        if ($platform->getName() !== 'mysql') {
            throw new DBALException('ENUMы не поддерживаются платформой.');
        }

        if (count($this->values) === 0) {
            throw new DBALException('Список значений для ENUM не должен быть пустым');
        }

        return 'ENUM(' . implode(',', array_map([$platform, 'quoteStringLiteral'], $this->values)) . ')';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function getName()
    {
        return $this->typeName;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
