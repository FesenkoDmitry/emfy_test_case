<?php

require_once __DIR__ . '/AmoWrap/AmoWrap.php';
require_once __DIR__ . '/AmoWrap/config.php';
require_once __DIR__ . '/AmoWrap/AmoNote.php';
require_once __DIR__ . '/Utils.php';

if (isset($_GET['action']) && $_GET['action'] == 'auth' && isset($_GET['code'])) {
    try {
        AmoWrap::getAccessTokenWithCode($_GET['code']);
    } catch (Exception $e) {
        //что-то делаем с ошибкой
        print_r($e);
    } finally {
        die;
    }
}

if (empty($_POST)) {
    die('Запрос пустой');
}

$entityType = array_keys($_POST)[1] ?? die('Неверный формат запроса');

if (!in_array($entityType, SUPPORTED_ENTITIES)) {
    die('Работа с сущностью не поддерживается');
}

$action = array_keys($_POST[$entityType])[0] ?? die('Неверный формат запроса');

if (!in_array($action, SUPPORTED_EVENTS)) {
    die('Работа с событием не поддерживается');
}

$entity = $_POST[$entityType][$action][0];

$entityId = $entity['id'];

$note = new AmoNote;
$note->setType('common');

$savedEntities = Utils::getSavedEntities($entityType);

if ($action === AMO_EVENT_ADD) {
    $note->setName($entity['name']);
    $note->setDateCreate(date('c', $entity['date_create']));
    try{
        $responsible = AmoWrap::getResponsibleNameById($entity['responsible_user_id']);
    } catch (Exception $e){
        //что-то делаем с ошибкой
        $responsible = 'Неизвестно';
    }
    $note->setResponsible($responsible);
    $savedEntities[] = $entity;
} else {
    $note->setDateModified(date('c', $entity['last_modified']));

    $foundSavedIndex = array_search($entityId, array_column($savedEntities, 'id'));
    $changedFields = [];

    if ($foundSavedIndex !== false) {
        $savedEntity = $savedEntities[$foundSavedIndex];
        foreach ($entity as $fieldId => $fieldValue) {
            if ((empty($savedEntity[$fieldId]) || $savedEntity[$fieldId] != $fieldValue) && !in_array($fieldId, FIELDS_NOT_TRACKING)) {
                $changedFields[$fieldId] = $fieldValue;
            }
            if ($fieldId == 'custom_fields') {
                foreach ($fieldValue as $customField) {
                    if (!empty($savedEntity['custom_fields'])) {
                        $foundCustomFieldIndex = array_search($customField['id'], array_column($savedEntity['custom_fields'], 'id'));
                        if ($foundCustomFieldIndex === false || $savedEntity['custom_fields'][$foundCustomFieldIndex]['values'][0]['value'] !== $customField['values'][0]['value']) {
                            $changedFields[$customField['name']] = $customField['values'][0]['value'];
                        }
                    } else {
                        $changedFields[$customField['name']] = $customField['values'][0]['value'];
                    }

                }
            }
        }
    } else {
        foreach ($entity as $fieldId => $fieldValue) {
            if (!in_array($fieldId, FIELDS_NOT_TRACKING)) {
                $changedFields[$fieldId] = $fieldValue;
            }
            if ($fieldId == 'custom_fields') {
                foreach ($fieldValue as $customField) {
                    $changedFields[$customField['name']] = $customField['values'][0]['value'];
                }
            }
        }
    }

    $savedEntities[$foundSavedIndex] = $entity;
    $note->setChangedFields($changedFields);
}

Utils::saveEntities($entityType, $savedEntities);

try{
    AmoWrap::addNote($entityType, $entityId, $note);
} catch (Exception $e){
    //что-то делаем с ошибкой
    die('Не удалось добавить примечение');
}
