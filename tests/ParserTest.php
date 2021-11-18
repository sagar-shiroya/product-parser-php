<?php

use App\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /**
     * Test Object of Parser Class
     *
     * @var object
     */
    private $test_parser;

    /**
     * Argument values passed from Terminal
     *
     * @var array
     */
    private $argument_values;

    public function setUp()
	{
        
        $this->argument_values = ["parser.php", "--file", "data/testexample.csv", "--unique-combination","combination_count.csv"];
        $this->test_parser = new Parser($this->argument_values);
    }

    /**
     * @covers ::setSupportedExtensions
     *
     * @return void
     */
    public function testSetSupportedExtensions()
    {
        $this->test_parser->setSupportedExtensions('input','csv');
        $this->assertEquals('csv',$this->test_parser->input_extensions_supported);
        $this->assertNotEquals('json',$this->test_parser->input_extensions_supported);


        $this->test_parser->setSupportedExtensions('output','tsv');
        $this->assertEquals('tsv',$this->test_parser->output_extension_supported);
        $this->assertNotEquals('csv',$this->test_parser->output_extension_supported);
    }

    /**
     * @covers ::setExtensionFromInputs
     *
     * @return void
     */
    public function testSetExtensionFromInputs()
    {
        $input_extension = pathinfo($this->argument_values[2]);
        $this->assertEquals($input_extension['extension'], $this->test_parser->input_file_extension);
        $this->assertEquals($this->argument_values[2], $this->test_parser->input_file_path);

        $output_extension = pathinfo($this->argument_values[4]);
        $this->assertEquals($output_extension['extension'], $this->test_parser->output_file_extension);
        $this->assertEquals($this->argument_values[4], $this->test_parser->output_file);
    }

    /**
     * @covers ::setHeaders
     *
     * @return void
     */
    public function testSetHeaders()
    {
        $mapped_headers = [
            "brand_name" => "Make",
            "model_name" => "Model"
        ];

        $this->test_parser->setHeaders($mapped_headers);
        $this->assertIsArray($this->test_parser->mapped_headers);
        $this->assertEquals($mapped_headers['brand_name'], $this->test_parser->mapped_headers['brand_name']);
        $this->assertNotEquals('make', $this->test_parser->mapped_headers['brand_name']);
    }

    /**
     * @covers ::run
     * @covers ::readInputsFromFile
     *
     * @return void
     */
    public function testRun()
    {
        $test_headers = [
            "brand_name" => "Make",
            "model_name" => "Model",
            "condition_name" => "Condition",
            "grade_name" => "Grade",
            "gb_spec_name" => "Capacity",
            "colour_name" => "Colour",
            "network_name" => "Network"
        ];
        $this->test_parser->setHeaders($test_headers);
        $this->test_parser->setSupportedExtensions('input', ['csv','tsv']);
        $this->test_parser->setSupportedExtensions('output', 'csv');
        $this->test_parser->output_file = 'test_output_file' . rand() . '.csv';
        $this->test_parser->run();

        /**
         * Check whether correct file exists or not
         */
        $this->assertFileExists(__DIR__."/..//data/output/".$this->test_parser->output_file);

        $this->assertFileNotExists(__DIR__."/..//data/output/".$this->argument_values[4]);

        /**
         * Check whether all headers from headers mapped array map correctly in output file
         */
        $handle = fopen(__DIR__."/..//data/output/".$this->test_parser->output_file, 'r');
        $header_array = fgetcsv($handle);
        $this->assertIsArray($header_array);

        $all_header_exists = true;
        foreach($test_headers as $key => $value) {
            if(!in_array($value,$header_array)) {
                $all_header_exists = false;
            }
        }
        $this->assertTrue($all_header_exists);
    }
}