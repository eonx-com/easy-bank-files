<?php

declare(strict_types=1);

namespace EonX\EasyBankFiles\Generators\Aba\Objects;

use EonX\EasyBankFiles\Generators\BaseObject;
use EonX\EasyBankFiles\Generators\Interfaces\GeneratorInterface;

/**
 * @method string getBlank1()
 * @method string getBlank2()
 * @method string getBlank3()
 * @method string getDateToBeProcessed()
 * @method string getDescriptionOfEntries()
 * @method string getNameOfUserSupplyingFile()
 * @method string getNumberOfUserSupplyingFile()
 * @method string getRecordType()
 * @method string getReelSequenceNumber()
 * @method string getUserFinancialInstitution()
 */
final class DescriptiveRecord extends BaseObject
{
    /**
     * BaseResult constructor.
     *
     * @param mixed[]|null $data
     */
    public function __construct(?array $data = null)
    {
        parent::__construct(\array_merge([
            'reelSequenceNumber' => '01',
        ], $data ?? []));
    }

    /**
     * Get validation rules.
     *
     * @return string[]
     */
    public function getValidationRules(): array
    {
        return [
            'nameOfUserSupplyingFile' => GeneratorInterface::VALIDATION_RULE_ALPHA,
            'numberOfUserSupplyingFile' => GeneratorInterface::VALIDATION_RULE_NUMERIC,
            'descriptionOfEntries' => GeneratorInterface::VALIDATION_RULE_ALPHA,
            'dateToBeProcessed' => GeneratorInterface::VALIDATION_RULE_DATE,
        ];
    }

    /**
     * Get attributes padding configuration as [<attribute> => [<length>, <string>, <type>]].
     *
     * @see http://php.net/manual/en/function.str-pad.php
     *
     * @return mixed[]
     */
    protected function getAttributesPaddingRules(): array
    {
        return [
            'blank1' => [17],
            'blank2' => [7],
            'blank3' => [40],
            'nameOfUserSupplyingFile' => [26],
            'numberOfUserSupplyingFile' => [6, '0', \STR_PAD_LEFT],
            'descriptionOfEntries' => [12],
        ];
    }

    /**
     * Return object attributes.
     *
     * @return string[]
     */
    protected function initAttributes(): array
    {
        return [
            'recordType',
            'blank1',
            'reelSequenceNumber',
            'userFinancialInstitution',
            'blank2',
            'nameOfUserSupplyingFile',
            'numberOfUserSupplyingFile',
            'descriptionOfEntries',
            'dateToBeProcessed',
            'blank3',
        ];
    }

    /**
     * Return record type.
     */
    protected function initRecordType(): string
    {
        return '0';
    }
}
