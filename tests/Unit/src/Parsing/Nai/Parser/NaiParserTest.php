<?php
declare(strict_types=1);

namespace EonX\EasyBankFiles\Tests\Unit\Parsing\Nai\Parser;

use EonX\EasyBankFiles\Parsing\Nai\Parser\NaiParser;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\Account;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\AccountSummaryCodesTrait;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\ControlTotalTrait;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\File;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\ResultsContext;
use EonX\EasyBankFiles\Parsing\Nai\ValueObject\TransactionDetailCodesTrait;
use EonX\EasyBankFiles\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AccountSummaryCodesTrait::class)]
#[CoversClass(ControlTotalTrait::class)]
#[CoversClass(NaiParser::class)]
#[CoversClass(ResultsContext::class)]
#[CoversClass(TransactionDetailCodesTrait::class)]
final class NaiParserTest extends AbstractUnitTestCase
{
    /**
     * ControlTotal should format amount as expected.
     */
    public function testControlTotalTraitReturnFormattedAmount(): void
    {
        $trait = $this->getObjectForTrait(ControlTotalTrait::class);

        self::assertIsFloat($this->callPrivateMethod($trait, 'formatAmount', '100000'));
        self::assertSame((float)100, $this->callPrivateMethod($trait, 'formatAmount', '10000'));
    }

    public function testParserCanParseBaiFile(): void
    {
        self::markTestSkipped('Until BAI sample added, however it was tested with real files');

        $bai = new NaiParser($this->getSampleFileContents('sample.BAI'));
        $nai = new NaiParser($this->getSampleFileContents('sample.NAI'));

        // Grab NAI accounts numbers
        $naiAccountNumbers = [];
        foreach ($nai->getAccounts() as $account) {
            $naiAccountNumbers[] = $account->getIdentifier()->getCommercialAccountNumber();
        }

        $naiTransactions = $this->getTransactionsForAccounts($nai->getAccounts());
        $baiTransactions = $this->getTransactionsForAccounts($bai->getAccounts(), $naiAccountNumbers);

        /**
         * @var \EonX\EasyBankFiles\Parsing\Nai\ValueObject\Transaction[] $transactions
         */
        foreach ($naiTransactions as $accountNumber => $transactions) {
            $currentBaiTransactions = $baiTransactions[$accountNumber];

            self::assertEquals(\count($transactions), \count($currentBaiTransactions));

            /**
             * @var int $index
             * @var \EonX\EasyBankFiles\Parsing\Nai\ValueObject\Transaction $transaction
             */
            foreach ($transactions as $index => $transaction) {
                $baiTransaction = $currentBaiTransactions[$index];

                // Assert fixed values
                self::assertEquals('0', $transaction->getFundsType());
                self::assertEquals('z', \strtolower((string)$baiTransaction->getFundsType()));

                self::assertEquals($transaction->getAmount(), $baiTransaction->getAmount(), 'mismatch amount');
                self::assertEquals($transaction->getCode(), $baiTransaction->getCode(), 'mismatch code');
                self::assertEquals(
                    $transaction->getReferenceNumber(),
                    $baiTransaction->getReferenceNumber(),
                    'mismatch reference number'
                );
                self::assertEquals($transaction->getText(), $baiTransaction->getText(), 'mismatch text');
                self::assertEquals(
                    $transaction->getTransactionCode(),
                    $baiTransaction->getTransactionCode(),
                    'mismatch transaction code'
                );

                $naiTransactionDetails = $transaction->getTransactionDetails();
                $baiTransactionDetails = $baiTransaction->getTransactionDetails();

                self::assertEquals(
                    $naiTransactionDetails->getDescription(),
                    $baiTransactionDetails->getDescription(),
                    'mismatch details description'
                );
                self::assertEquals(
                    $naiTransactionDetails->getParticulars(),
                    $baiTransactionDetails->getParticulars(),
                    'mismatch details particulars'
                );
                self::assertEquals(
                    $naiTransactionDetails->getType(),
                    $baiTransactionDetails->getType(),
                    'mismatch details type'
                );
            }
        }
    }

    /**
     * Parser should handle structure errors as expected.
     */
    public function testParserHandleStructureErrorAsExpected(): void
    {
        $parser = new NaiParser($this->getSampleFileContents('structure_errors.NAI'));

        self::assertCount(8, $parser->getErrors());
    }

    /**
     * Parser should parse sample file successfully.
     */
    public function testParserParsesSuccessfully(): void
    {
        $parser = new NaiParser($this->getSampleFileContents('sample.NAI'));

        self::assertInstanceOf(File::class, $parser->getFile());
        /** @var \EonX\EasyBankFiles\Parsing\Nai\ValueObject\File $file */
        $file = $parser->getFile();
        self::assertSame('BNZA', $file->getHeader()->getReceiverId());
        self::assertCount(1, $parser->getGroups());
        self::assertCount(4, $parser->getAccounts());
        self::assertCount(6, $parser->getTransactions());
        self::assertCount(2, $parser->getErrors());

        $transactions = $parser->getTransactions();

        self::assertSame('NEW MULTI TFRDEBIT 5148       PYMT-ID 00000000 492672', $transactions[5]->getText());
    }

    /**
     * Parser should parse sample file successfully.
     * This tests a file which has slashes in the transaction records.
     */
    public function testParserParsesSuccessfullyWhenFileHasTransactionWithSlashes(): void
    {
        $parser = new NaiParser($this->getSampleFileContents('nab_sample.NAI'));

        self::assertInstanceOf(File::class, $parser->getFile());
        /** @var \EonX\EasyBankFiles\Parsing\Nai\ValueObject\File $file */
        $file = $parser->getFile();
        self::assertSame('BNZA', $file->getHeader()->getReceiverId());
        self::assertCount(1, $parser->getGroups());
        self::assertCount(4, $parser->getAccounts());
        self::assertCount(10, $parser->getTransactions());
        // One error line, 16,475,330/ .. because its missing required fundType
        self::assertCount(1, $parser->getErrors());

        $transactions = $parser->getTransactions();

        $expectedTransactions = [
            [
                'amount' => '64598',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0',
                'text' => 'ABC DEF',
                'transactionCode' => '936',
            ],
            [
                'amount' => '70050',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005607',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '22410',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005712',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '22650',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005820',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '210620',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005924',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '379200',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005956',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '61915',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0005968',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '3300000',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0006100',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '330',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '',
                'text' => '',
                'transactionCode' => '475',
            ],
            [
                'amount' => '104410',
                'code' => '16',
                'fundsType' => '0',
                'referenceNumber' => '0',
                'text' => 'AP8YA0436912      GEDFH            083310',
                'transactionCode' => '501',
            ],
        ];

        $actualTransactions = [];
        foreach ($transactions as $transaction) {
            $actualTransactions[] = [
                'amount' => $transaction->getAmount(),
                'code' => $transaction->getCode(),
                'fundsType' => $transaction->getFundsType(),
                'referenceNumber' => $transaction->getReferenceNumber(),
                'text' => $transaction->getText(),
                'transactionCode' => $transaction->getTransactionCode(),
            ];
        }

        self::assertSame($expectedTransactions, $actualTransactions);
    }

    /**
     * Transaction codes detail trait should return null if code is invalid.
     *
     * @throws \ReflectionException
     */
    public function testTransactionCodesTraitReturnNullWhenInvalidCode(): void
    {
        $trait = $this->getObjectForTrait(TransactionDetailCodesTrait::class);

        self::assertNull($trait->getTransactionCodeDetails('invalid'));
    }

    public function testTrickyFile(): void
    {
        $parser = new NaiParser($this->getSampleFileContents('tricky.NAI'));
        $expected = 'STUART SMALL        FRKXMT8BK5          FRKXMT8BK5 Stuart Sm';

        self::assertCount(1, $parser->getTransactions());
        self::assertEquals($expected, $parser->getTransactions()[0]->getText());
    }

    private function getSampleFileContents(string $file): string
    {
        return \file_get_contents(
            \realpath(__DIR__) . '/../../../../../Fixture/Parsing/Nai/' . $file
        ) ?: '';
    }

    /**
     * @param \EonX\EasyBankFiles\Parsing\Nai\ValueObject\Account[] $accounts
     * @param string[]|null $filter The account numbers to filter on
     *
     * @return array<string, \EonX\EasyBankFiles\Parsing\Nai\ValueObject\Transaction[]>
     */
    private function getTransactionsForAccounts(array $accounts, ?array $filter = null): array
    {
        $return = [];
        $filter ??= \array_map(
            static fn (Account $account): string => $account->getIdentifier()
                ->getCommercialAccountNumber(),
            $accounts
        );

        foreach ($accounts as $account) {
            $accountNumber = $account->getIdentifier()
                ->getCommercialAccountNumber();

            if (\in_array($accountNumber, $filter, true) === false) {
                continue;
            }

            $return[$accountNumber] = $account->getTransactions();
        }

        return $return;
    }
}
