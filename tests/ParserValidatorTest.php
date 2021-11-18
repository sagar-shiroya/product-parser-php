<?php

use App\Parser;
use App\ParserValidator;
use PHPUnit\Framework\TestCase;

class ParserValidatorTest extends TestCase
{
    /**
     * Test Object of Parser Class
     *
     * @var object
     */
    protected $test_parser;

    /**
     * Test Object of Parser Validator Class
     *
     * @var object
     */
    protected $test_parser_validator;

    /**
     * Arguments passed from Terminal
     *
     * @var array
     */
    protected $argument_values;

    public function setUp()
	{
        
        $this->argument_values = ["parser.php", "--file", "data/testexample.csv", "--unique-combination","combination_count.csv"];
        $this->test_parser = new Parser($this->argument_values);
        $this->test_parser->setSupportedExtensions('input','csv');
        $this->test_parser->setSupportedExtensions('output','csv');
    }

    /**
     * @covers ::checkExtension
     *
     * @return void
     */
    public function testCheckExtension()
    {
        $this->test_parser_validator = new ParserValidator($this->test_parser);

        /**
         * Positive Cases
         */
        $result = $this->test_parser_validator->checkExtension('csv','csv');
        $this->assertEquals(true, $result);

        $result = $this->test_parser_validator->checkExtension(['xml'],'XML');
        $this->assertTrue($result);

        $result = $this->test_parser_validator->checkExtension('CsV','csv');
        $this->assertTrue($result);

        $result = $this->test_parser_validator->checkExtension(['csv','json'],'csv');
        $this->assertTrue($result);

        /**
         * False Cases
         */
        $result = $this->test_parser_validator->checkExtension(['csv','json'],'xml');
        $this->assertFalse($result);

        $result = $this->test_parser_validator->checkExtension('json','csv');
        $this->assertFalse($result);
    }

    /**
     * @covers ::fileFormatValidator
     *
     * @return void
     */
    public function testFileFormatValidator()
    {
        try {
            $this->test_parser->setSupportedExtensions('input','json');
            $validator = new ParserValidator($this->test_parser);
            $validator->fileFormatValidator();
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            $this->assertRegExp('/Input File format not supported/',$exception_message);
        }

        try {
            $this->test_parser->setSupportedExtensions('input','csv');
            $this->test_parser->setSupportedExtensions('output','json');
            $validator2 = new ParserValidator($this->test_parser);
            $validator2->fileFormatValidator();
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            $this->assertRegExp('/Output File format not supported/',$exception_message);
        } 
    }

    /**
     * @covers ::validateInputs
     *
     * @return void
     */
    public function testValidateInputs()
    {
        try {
            $this->argument_values[1] = 'file';
            ParserValidator::validateInputs($this->argument_values);
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            $this->assertRegExp('/Invalid Input Format/',$exception_message);
        }
        
        try {
            $this->argument_values[1] = '--file';
            $this->argument_values[2] = 'test2.csv';
            ParserValidator::validateInputs($this->argument_values);
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            $this->assertRegExp('/not exists/',$exception_message);
        } 
    }

    /**
     * @covers ::inputHeaderValidation
     * 
     * @return void
     */
    public function testInputHeaderValidation()
    {
        try {
            $input_output_header_mapped = [
                "brand_name" => "Make",
                "model_name" => "Model",
                "condition_name" => "Condition"
            ];
            $this->test_parser->setHeaders($input_output_header_mapped);
            $validator = new ParserValidator($this->test_parser);
            $headers_arr = $this->test_parser->readInputsFromFile($this->argument_values[2]);
            $validator->inputHeaderValidation($headers_arr);
        } catch (Exception $e) {
            $exception_message = $e->getMessage();
            $this->assertRegExp('/column not exists in Input Headers Array/',$exception_message);
        }
    }
}