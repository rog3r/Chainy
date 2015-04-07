<?php

chdir(realpath(dirname(__FILE__) . '/../web'));
require_once 'config.php';

use \AmiLabs\Chainy\TX;

class Chainy_TXTest extends PHPUnit_Framework_TestCase{
    /**
     * @covers \AmiLabs\Chainy\TX::testGetBlockDate
     */
    public function testGetBlockDate(){
        // Ordinary block timestamp
        $blockDate = TX::getBlockDate(350000, FALSE);
        $this->assertEquals(1427753834, $blockDate);
        // Ordinary block date
        $blockDate = TX::getBlockDate(350000);
        $this->assertEquals(date('Y-m-d H:i:s', 1427753834), $blockDate);
        // Ordinary block date in custom format
        $format = 'd-i:s H:Y-m';
        $blockDate = TX::getBlockDate(350000, $format);
        $this->assertEquals(date($format, 1427753834), $blockDate);
        // Zero block
        $blockDate = TX::getBlockDate(0);
        $this->assertEquals(FALSE, $blockDate);
        // Unexisting block
        $blockDate = TX::getBlockDate(100000000);
        $this->assertEquals(FALSE, $blockDate);
        // Not a block number
        $blockDate = TX::getBlockDate('test');
        $this->assertEquals(FALSE, $blockDate);
    }
    /**
     * @covers \AmiLabs\Chainy\TX::getPositionInBlockByTransaction
     */
    public function testGetPositionInBlockByTransaction(){
        // Valid transaction
        $aPosition = TX::getPositionInBlockByTransaction('a716ab62a35baa0aa75bb675f8e479b212fc45b1b5320faadd6b0b0ed74e426e');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(349633, $aPosition['block']);
        $this->assertEquals(634, $aPosition['position']);
        // Valid transaction
        $aPosition = TX::getPositionInBlockByTransaction('93b79d48ebcf34e176ee6a785cef329c45e70b9a47ae66284b8fc42ca297570b');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(347417, $aPosition['block']);
        $this->assertEquals(1191, $aPosition['position']);
        // Invalid transaction
        $aPosition = TX::getPositionInBlockByTransaction('invalid_transaction_hash');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
        // Invalid transaction
        $aPosition = TX::getPositionInBlockByTransaction(123);
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
        // Zero transaction
        $aPosition = TX::getPositionInBlockByTransaction(false);
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
    }
    /**
     * @covers \AmiLabs\Chainy\TX::getTransactionByPositionInBlock
     */
    public function testGetTransactionByPositionInBlock(){
        // Valid transaction
        $txn = TX::getTransactionByPositionInBlock(349633, 634);
        $this->assertEquals('a716ab62a35baa0aa75bb675f8e479b212fc45b1b5320faadd6b0b0ed74e426e', $txn);
        // Valid transaction
        $txn = TX::getTransactionByPositionInBlock(347417, 1191);
        $this->assertEquals('93b79d48ebcf34e176ee6a785cef329c45e70b9a47ae66284b8fc42ca297570b', $txn);
        // Zero position
        $txn = TX::getTransactionByPositionInBlock(350000, 0);
        $this->assertEquals(NULL, $txn);
        // Invalid position
        $txn = TX::getTransactionByPositionInBlock(350000, -1);
        $this->assertEquals(NULL, $txn);
        // Invalid position
        $txn = TX::getTransactionByPositionInBlock(350000, 100000);
        $this->assertEquals(NULL, $txn);
        // Zero block
        $txn = TX::getTransactionByPositionInBlock(0, 1);
        $this->assertEquals(NULL, $txn);
        // Invalid block
        $txn = TX::getTransactionByPositionInBlock(-1, 1);
        $this->assertEquals(NULL, $txn);
        // Invalid block
        $txn = TX::getTransactionByPositionInBlock(100000000, 1);
        $this->assertEquals(NULL, $txn);
    }
    /**
     * @covers \AmiLabs\Chainy\TX::isChainyTransaction
     */
    public function testIsChainyTransaction(){
        // Valid Chainy (redirect, production marker)
        $isChainy = TX::isChainyTransaction('4844e5edc2bb05bc2dbb416048da09288f4fb31b62ab489f2788e262ea8a42c5');
        $this->assertEquals(TRUE, $isChainy);
        // Valid Chainy (filehash, production marker)
        $isChainy = TX::isChainyTransaction('cc68babc421b926a1e717a6aaadc88b0b61dce7c4227a5c25a3054d97568b910');
        $this->assertEquals(TRUE, $isChainy);
        // Valid Chainy (redirect, development marker)
        $isChainy = TX::isChainyTransaction('9409ab2b2fcc200e13496efed876101a76d84a50f528bcf7ed3b22e51ac8ac41');
        $this->assertEquals(TRUE, $isChainy);
        // Valid Chainy (filehash, development marker)
        $isChainy = TX::isChainyTransaction('f9b6342b21f354a679f4761572c117fd807a52164fb6297c4d5a0f1b9d0224a3');
        $this->assertEquals(TRUE, $isChainy);
        // Not a Chainy transaction
        $isChainy = TX::isChainyTransaction('dbf9a3c5bd5441e2e0a1facf837880049c580de7d4b77b320c0d2a576a2846ca');
        $this->assertEquals(FALSE, $isChainy);
        // Not a Chainy transaction (no OP_RETURN)
        $isChainy = TX::isChainyTransaction('68dfc30898bf095058691f5740af98fcfdf30635f077e461565c9a2ace45e323');
        $this->assertEquals(FALSE, $isChainy);
        // Invalid hash
        $isChainy = TX::isChainyTransaction('invalid_transaction_hash');
        $this->assertEquals(FALSE, $isChainy);
        // False hash
        $isChainy = TX::isChainyTransaction(false);
        $this->assertEquals(FALSE, $isChainy);
    }

    public function testGetTransactionType(){
        // Valid Chainy (redirect, production marker)
        $type = TX::getTransactionType('4844e5edc2bb05bc2dbb416048da09288f4fb31b62ab489f2788e262ea8a42c5');
        $this->assertEquals(TX::TX_TYPE_REDIRECT, $type);
        // Valid Chainy (filehash, production marker)
        $type = TX::getTransactionType('cc68babc421b926a1e717a6aaadc88b0b61dce7c4227a5c25a3054d97568b910');
        $this->assertEquals(TX::TX_TYPE_HASHLINK, $type);
        // Valid Chainy (redirect, development marker)
        $type = TX::getTransactionType('9409ab2b2fcc200e13496efed876101a76d84a50f528bcf7ed3b22e51ac8ac41');
        $this->assertEquals(TX::TX_TYPE_REDIRECT, $type);
        // Valid Chainy (filehash, development marker)
        $type = TX::getTransactionType('f9b6342b21f354a679f4761572c117fd807a52164fb6297c4d5a0f1b9d0224a3');
        $this->assertEquals(TX::TX_TYPE_HASHLINK, $type);
        // Not a Chainy transaction
        $type = TX::getTransactionType('dbf9a3c5bd5441e2e0a1facf837880049c580de7d4b77b320c0d2a576a2846ca');
        $this->assertEquals(TX::TX_TYPE_INVALID, $type);
        // Not a Chainy transaction (no OP_RETURN)
        $type = TX::getTransactionType('68dfc30898bf095058691f5740af98fcfdf30635f077e461565c9a2ace45e323');
        $this->assertEquals(TX::TX_TYPE_INVALID, $type);
        // Invalid hash
        $type = TX::getTransactionType('invalid_transaction_hash');
        $this->assertEquals(TX::TX_TYPE_INVALID, $type);
        // False hash
        $type = TX::getTransactionType(false);
        $this->assertEquals(TX::TX_TYPE_INVALID, $type);

    }
}