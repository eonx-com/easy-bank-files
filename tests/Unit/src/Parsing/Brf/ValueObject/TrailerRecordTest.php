<?php
declare(strict_types=1);

namespace EonX\EasyBankFiles\Tests\Unit\Parsing\Brf\ValueObject;

use EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException;
use EonX\EasyBankFiles\Parsing\Brf\ValueObject\TrailerRecord;
use EonX\EasyBankFiles\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

#[CoversClass(TrailerRecord::class)]
final class TrailerRecordTest extends AbstractUnitTestCase
{
    /**
     * Should return amount of error corrections.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnAmountOfErrorCorrections(): void
    {
        $expected = [
            'amount' => '2000',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'amountOfErrorCorrections' => '00000000000200{',
        ]);

        self::assertIsArray($trailer->getAmountOfErrorCorrections());
        self::assertSame($expected, $trailer->getAmountOfErrorCorrections());
    }

    /**
     * Should return amount of payments.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnAmountOfPayments(): void
    {
        $expected = [
            'amount' => '12015',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'amountOfPayments' => '00000000001201E',
        ]);

        self::assertIsArray($trailer->getAmountOfPayments());
        self::assertSame($expected, $trailer->getAmountOfPayments());
    }

    /**
     * Should return amount of payments.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnAmountOfReversals(): void
    {
        $expected = [
            'amount' => '12517',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'amountOfReversals' => '00000000001251G',
        ]);

        self::assertIsArray($trailer->getAmountOfReversals());
        self::assertSame($expected, $trailer->getAmountOfReversals());
    }

    /**
     * Should return number of error corrections.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnNumberOfErrorCorrections(): void
    {
        $expected = [
            'amount' => '10',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'numberOfErrorCorrections' => '00000001{',
        ]);

        self::assertCount(2, $trailer->getNumberOfErrorCorrections());
        self::assertSame($expected, $trailer->getNumberOfErrorCorrections());
    }

    /**
     * Should return number of payments.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnNumberOfPayments(): void
    {
        $expected = [
            'amount' => '34',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'numberOfPayments' => '00000003D',
        ]);

        self::assertCount(2, $trailer->getNumberOfPayments());
        self::assertSame($expected, $trailer->getNumberOfPayments());
    }

    /**
     * Should return number of reversals.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnNumberOfReversals(): void
    {
        $expected = [
            'amount' => '20',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'numberOfReversals' => '00000002{',
        ]);

        self::assertCount(2, $trailer->getNumberOfReversals());
        self::assertSame($expected, $trailer->getNumberOfReversals());
    }

    /**
     * Should return settlement amount.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldReturnSettlementAmount(): void
    {
        $expected = [
            'amount' => '12517',
            'type' => 'credit',
        ];

        $trailer = new TrailerRecord([
            'settlementAmount' => '00000000001251G',
        ]);

        self::assertCount(2, $trailer->getSettlementAmount());
        self::assertSame($expected, $trailer->getSettlementAmount());
    }

    /**
     * Should throw exception if sign field is not found.
     *
     * @throws \EonX\EasyBankFiles\Parsing\Brf\Exception\InvalidSignFieldException
     */
    #[Group('Brf-Trailer')]
    public function testShouldThrowExceptionIfSignedFileNotFound(): void
    {
        $this->expectException(InvalidSignFieldException::class);

        $trailer = new TrailerRecord([
            'amountOfErrorCorrections' => '00000000000200W',
        ]);

        $trailer->getAmountOfErrorCorrections();
    }
}
