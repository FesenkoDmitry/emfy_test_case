<?php


class Utils
{

    /**
     * @param string $entityType
     * @return array
     */
    public static function getSavedEntities(string $entityType): array
    {
        $savedEntitiesFile = __DIR__ . "/AmoWrap/$entityType.json";
        if (file_exists($savedEntitiesFile)) {
            $savedEntities = json_decode(file_get_contents($savedEntitiesFile), true);
        } else {
            $savedEntities = [];
        }

        return $savedEntities;
    }

    /**
     * @param string $entityType
     * @param array $entitiesToSave
     */
    public static function saveEntities(string $entityType, array $entitiesToSave): void
    {
        $savedEntitiesFile = __DIR__ . "/AmoWrap/$entityType.json";
        file_put_contents($savedEntitiesFile, json_encode($entitiesToSave));
    }
}