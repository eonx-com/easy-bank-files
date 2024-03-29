<?php
declare(strict_types=1);

namespace EonX\EasyBankFiles\Parsers\Bpay\Brf;

use EonX\EasyBankFiles\Parsers\AbstractLineByLineParser;
use EonX\EasyBankFiles\Parsers\Bpay\Brf\Results\Header;
use EonX\EasyBankFiles\Parsers\Bpay\Brf\Results\Trailer;
use EonX\EasyBankFiles\Parsers\Bpay\Brf\Results\Transaction;
use EonX\EasyBankFiles\Parsers\Error;

final class Parser extends AbstractLineByLineParser
{
    private const HEADER = '00';

    private const TRAILER = '99';

    private const TRANSACTION = '50';

    /**
     * @var \EonX\EasyBankFiles\Parsers\Error[] $errors
     */
    private array $errors = [];

    private Header $header;

    private Trailer $trailer;

    /**
     * @var \EonX\EasyBankFiles\Parsers\Bpay\Brf\Results\Transaction[] $transactions
     */
    private array $transactions = [];

    /**
     * @return \EonX\EasyBankFiles\Parsers\Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Return the Header object.
     */
    public function getHeader(): Header
    {
        return $this->header;
    }

    /**
     * Return the Trailer object.
     */
    public function getTrailer(): Trailer
    {
        return $this->trailer;
    }

    /**
     * @return \EonX\EasyBankFiles\Parsers\Bpay\Brf\Results\Transaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * Process line and parse data.
     */
    protected function processLine(int $lineNumber, string $line): void
    {
        $code = \substr($line, 0, 2);

        switch ($code) {
            case self::HEADER:
                $this->header = $this->processHeader($line);

                break;

            case self::TRANSACTION:
                $this->transactions[] = $this->processTransaction($line);

                break;

            case self::TRAILER:
                $this->trailer = $this->processTrailer($line);

                break;

            default:
                $this->errors[] = new Error(\compact('line', 'lineNumber'));

                break;
        }
    }

    /**
     * Parse header.
     */
    private function processHeader(string $line): Header
    {
        /** @var string|false $billerCode */
        $billerCode = \substr($line, 2, 10);
        /** @var string|false $billerShortName */
        $billerShortName = \substr($line, 12, 20);
        /** @var string|false $billerCreditBSB */
        $billerCreditBSB = \substr($line, 32, 6);
        /** @var string|false $billerCreditAccount */
        $billerCreditAccount = \substr($line, 38, 9);
        /** @var string|false $fileCreationDate */
        $fileCreationDate = \substr($line, 47, 8);
        /** @var string|false $fileCreationTime */
        $fileCreationTime = \substr($line, 55, 6);
        /** @var string|false $restOfRecord */
        $restOfRecord = \substr($line, 61, 158);

        return new Header([
            'billerCode' => $billerCode === false ? null : $this->trimLeftZeros($billerCode),
            'billerCreditAccount' => $billerCreditAccount === false ? null : $billerCreditAccount,
            'billerCreditBSB' => $billerCreditBSB === false ? null : $billerCreditBSB,
            'billerShortName' => $billerShortName === false ? null : \trim($billerShortName),
            'fileCreationDate' => $fileCreationDate === false ? null : $fileCreationDate,
            'fileCreationTime' => $fileCreationTime === false ? null : $fileCreationTime,
            'restOfRecord' => $restOfRecord === false ? null : $restOfRecord,
        ]);
    }

    /**
     * Parse trailer.
     */
    private function processTrailer(string $line): Trailer
    {
        /** @var string|false $billerCode */
        $billerCode = \substr($line, 2, 10);
        /** @var string|false $numberOfPayments */
        $numberOfPayments = \substr($line, 12, 9);
        /** @var string|false $amountOfPayments */
        $amountOfPayments = \substr($line, 21, 15);
        /** @var string|false $numberOfErrorCorrections */
        $numberOfErrorCorrections = \substr($line, 36, 9);
        /** @var string|false $amountOfErrorCorrections */
        $amountOfErrorCorrections = \substr($line, 45, 15);
        /** @var string|false $numberOfReversals */
        $numberOfReversals = \substr($line, 60, 9);
        /** @var string|false $amountOfReversals */
        $amountOfReversals = \substr($line, 69, 15);
        /** @var string|false $settlementAmount */
        $settlementAmount = \substr($line, 84, 15);
        /** @var string|false $restOfRecord */
        $restOfRecord = \substr($line, 99, 120);

        return new Trailer([
            'amountOfErrorCorrections' => $amountOfErrorCorrections === false
                ? null
                : $this->trimLeftZeros($amountOfErrorCorrections),
            'amountOfPayments' => $amountOfPayments === false ? null : $this->trimLeftZeros($amountOfPayments),
            'amountOfReversals' => $amountOfReversals === false ? null : $this->trimLeftZeros($amountOfReversals),
            'billerCode' => $billerCode === false ? null : $this->trimLeftZeros($billerCode),
            'numberOfErrorCorrections' => $numberOfErrorCorrections === false
                ? null
                : $this->trimLeftZeros($numberOfErrorCorrections),
            'numberOfPayments' => $numberOfPayments === false ? null : $this->trimLeftZeros($numberOfPayments),
            'numberOfReversals' => $numberOfReversals === false ? null : $this->trimLeftZeros($numberOfReversals),
            'restOfRecord' => $restOfRecord === false ? null : $restOfRecord,
            'settlementAmount' => $settlementAmount === false ? null : $this->trimLeftZeros($settlementAmount),
        ]);
    }

    /**
     * Parse transaction items.
     */
    private function processTransaction(string $line): Transaction
    {
        /** @var string|false $billerCode */
        $billerCode = \substr($line, 2, 10);
        /** @var string|false $customerReferenceNumber */
        $customerReferenceNumber = \substr($line, 12, 20);
        /** @var string|false $paymentInstructionType */
        $paymentInstructionType = \substr($line, 32, 2);
        /** @var string|false $transactionReferenceNumber */
        $transactionReferenceNumber = \substr($line, 34, 21);
        /** @var string|false $originalReferenceNumber */
        $originalReferenceNumber = \substr($line, 55, 21);
        /** @var string|false $errorCorrectionReason */
        $errorCorrectionReason = \substr($line, 76, 3);
        /** @var string|false $amount */
        $amount = \substr($line, 79, 12);
        /** @var string|false $paymentDate */
        $paymentDate = \substr($line, 91, 8);
        /** @var string|false $paymentTime */
        $paymentTime = \substr($line, 99, 6);
        /** @var string|false $settlementDate */
        $settlementDate = \substr($line, 105, 8);
        /** @var string|false $restOfRecord */
        $restOfRecord = \substr($line, 113, 106);

        return new Transaction([
            'amount' => $amount === false ? null : $this->trimLeftZeros($amount),
            'billerCode' => $billerCode === false ? null : $this->trimLeftZeros($billerCode),
            'customerReferenceNumber' => $customerReferenceNumber === false ? null : \trim($customerReferenceNumber),
            'errorCorrectionReason' => $errorCorrectionReason === false ? null : $errorCorrectionReason,
            'originalReferenceNumber' => $originalReferenceNumber === false ? null : \trim($originalReferenceNumber),
            'paymentDate' => $paymentDate === false ? null : $paymentDate,
            'paymentInstructionType' => $paymentInstructionType === false ? null : $paymentInstructionType,
            'paymentTime' => $paymentTime === false ? null : $paymentTime,
            'restOfRecord' => $restOfRecord === false ? null : $restOfRecord,
            'settlementDate' => $settlementDate === false ? null : $settlementDate,
            'transactionReferenceNumber' => $transactionReferenceNumber === false
                ? null
                : \trim($transactionReferenceNumber),
        ]);
    }
}
