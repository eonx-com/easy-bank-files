<?php
declare(strict_types=1);

namespace EonX\EasyBankFiles\Parsing\DirectEntryBatch\ValueObject;

use EonX\EasyBankFiles\Parsing\Common\ValueObject\AbstractResult;

/**
 * @method string|null getAccountName()
 * @method string|null getAccountNumber()
 * @method string|null getAmount()
 * @method string|null getBsb()
 * @method string|null getLodgmentReference()
 * @method string|null getOriginalDayOfReturn()
 * @method string|null getOriginalUserIdNumber()
 * @method string getRecordType()
 * @method string getRefusalCode()
 * @method string|null getRemitterName()
 * @method string|null getTraceAccountNumber()
 * @method string|null getTraceBsb()
 * @method string|null getTransactionCode()
 */
final class RefusalDetailRecord extends AbstractResult
{
    /**
     * @return string[]
     */
    protected function initAttributes(): array
    {
        return [
            'accountName',
            'accountNumber',
            'amount',
            'bsb',
            'lodgmentReference',
            'originalDayOfReturn',
            'originalUserIdNumber',
            'recordType',
            'refusalCode',
            'remitterName',
            'traceAccountNumber',
            'traceBsb',
            'transactionCode',
        ];
    }
}
