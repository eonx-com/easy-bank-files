<?php
declare(strict_types=1);

namespace EonX\EasyBankFiles\Parsing\DirectEntryBatch\ValueObject;

use EonX\EasyBankFiles\Parsing\Common\ValueObject\AbstractResult;

/**
 * @method string|null getAccountName()
 * @method string|null getAccountNumber()
 * @method string|null getAmount()
 * @method string|null getBsb()
 * @method string getIndicator()
 * @method string|null getLodgmentReference()
 * @method string getRecordType()
 * @method string|null getRemitterName()
 * @method string|null getTraceAccountNumber()
 * @method string|null getTraceBsb()
 * @method string|null getTransactionCode()
 * @method string|null getAmountOfWithholdingTax()
 */
final class PaymentDetailRecord extends AbstractResult
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
            'indicator',
            'lodgmentReference',
            'recordType',
            'remitterName',
            'traceAccountNumber',
            'traceBsb',
            'transactionCode',
            'amountOfWithholdingTax',
        ];
    }
}
