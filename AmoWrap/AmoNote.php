<?php


class AmoNote
{
    /**
     * @var array
     */
    private array $name;
    /**
     * @var array
     */
    private array $responsible;
    /**
     * @var array
     */
    private array $dateCreate;
    /**
     * @var array
     */
    private array $dateModified;
    /**
     * @var string
     */
    private string $type;
    /**
     * @var array
     */
    private array $changedFields;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = [
            'name' => 'Название: ',
            'value' => $name
        ];
    }

    /**
     * @param string $responsible
     */
    public function setResponsible(string $responsible): void
    {
        $this->responsible = [
            'name' => 'Ответственный: ',
            'value' => $responsible
        ];
    }

    /**
     * @param string $dateCreate
     */
    public function setDateCreate(string $dateCreate): void
    {
        $this->dateCreate = [
            'name' => 'Дата создания: ',
            'value' => $dateCreate
        ];
    }

    /**
     * @param string $dateModified
     */
    public function setDateModified(string $dateModified): void
    {
        $this->dateModified = [
            'name' => 'Дата изменения: ',
            'value' => $dateModified
        ];
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param array $changedFields
     */
    public function setChangedFields(array $changedFields): void
    {
        $this->changedFields = $changedFields;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        var_dump($this);
        $vars = get_object_vars($this);
        $assignedValues = [];
        foreach ($vars as $name => $property) {
            if (!empty($property) && $name != 'type') {
                if (empty($property['name'])) {
                    $customFieldsValues = '';
                    foreach ($property as $customFieldName => $customFieldValue) {
                        $customFieldsValues .= $customFieldName . ' = ' . $customFieldValue . ', ';
                    }
                    $assignedValues[] = $customFieldsValues;
                } else {
                    $assignedValues[] = $property['name'] . $property['value'];
                }
            }
        }


        if (empty($assignedValues)) {
            $text = 'Измененных полей не было';
        } else {
            $text = implode(', ', $assignedValues);
        }

        return [
            'note_type' => $this->type,
            'text' => $text
        ];

    }
}