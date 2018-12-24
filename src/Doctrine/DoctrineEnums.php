<?php

namespace Nalogka\EnumType\Doctrine;

use Doctrine\DBAL\Types\Type;
use Nalogka\EnumType\Annotation\DefineEnum;
use Nalogka\EnumType\DBAL\Types\EnumType;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineEnums
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param Reader $annotations
     * @return EntityManagerInterface
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function registerEnums(EntityManagerInterface $entityManager, Reader $annotations)
    {
        $databasePlatform = $entityManager->getConnection()->getDatabasePlatform();
        foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $classMetadata) {
            $class = $classMetadata->getReflectionClass();
            if ($annotation = $annotations->getClassAnnotation($class, DefineEnum::class)) {
                self::registerEnum($annotation->typeName, $annotation->values, $databasePlatform);
            }
            foreach ($classMetadata->getFieldNames() as $fieldName) {
                if ($class->hasProperty($fieldName)) { // поле может быть унаследовано
                    $property = $class->getProperty($fieldName);
                    if ($annotation = $annotations->getPropertyAnnotation($property, DefineEnum::class)) {
                        self::registerEnum($annotation->typeName, $annotation->values, $databasePlatform);
                    }
                }
            }
        }

        return $entityManager;
    }

    /**
     * @param string           $type
     * @param array            $values
     * @param AbstractPlatform $databasePlatform
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private static function registerEnum(string $type, array $values, AbstractPlatform $databasePlatform)
    {
        /** @var DefineEnum $annotation */
        if (Type::hasType($type)) {
            /** @var EnumType $typeObject */
            $typeObject = Type::getType($type);
            if (!$typeObject instanceof EnumType) {
                throw new \RuntimeException(sprintf('Ошибка переопределения типа "%s"', $type));
            } elseif (array_diff($typeObject->values, self::flattenArray($values))) {
                throw new \RuntimeException(sprintf('Ошибка переопределения ENUM::ENUM типа "%s"', $type));
            }
        } else {
            Type::addType($type, EnumType::class);
            /** @var EnumType $typeObject */
            $typeObject = Type::getType($type);
            $typeObject->typeName = $type;
            $typeObject->values = self::flattenArray($values);
            $databasePlatform->registerDoctrineTypeMapping($type, $type);
        }
    }

    private static function flattenArray(array $values)
    {
        $result = [];
        foreach ($values as $value) {
            if (is_array($value)) {
                $result = array_merge($result, self::flattenArray($value));
            } else {
                $result[] = $value;
            }
        }

        return array_unique($result);
    }
}
