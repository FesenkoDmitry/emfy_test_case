<?php

const CLIENT_ID = 'dc988499-923e-4aee-a370-f41df31ac63f';
const CLIENT_SECRET = '6FIi0TjGmCOLshstdQOxw2o81rztfKlFHL1nV5iXa3FHwfbWNZYCija2TRZTMujU';
const REDIRECT_URI = 'https://yourarun.ru/emfy/index.php';
const AMO_DOMAIN = 'https://emfytestcasemymailru.amocrm.ru/';

const AMO_EVENT_ADD = 'add';
const AMO_EVENT_UPDATE = 'update';

const SUPPORTED_ENTITIES = [
    'leads',
    'contacts'
];

const SUPPORTED_EVENTS = [
    AMO_EVENT_ADD,
    AMO_EVENT_UPDATE
];

const FIELDS_NOT_TRACKING = [
    'id',
    'last_modified',
    'modified_user_id',
    'created_user_id',
    'date_create',
    'account_id',
    'custom_fields',
    'created_at',
    'updated_at',
    'old_responsible_user_id'
];