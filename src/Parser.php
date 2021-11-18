<?php

namespace App;

use Exception;
use App\ParserValidator;

/**
 * Parser class is the main parser class.
 * 
 * This class will set all member variables, call validator for inputs entered from terminal.
 * 
 * @copyright 2021 Sagar Shiroya
 */
class Parser
{
    /**
     * All the input file formats supported for the parser
     *
     * @var array $input_extensions_supported
     */
    public $input_extensions_supported;

    /**
     * Output file format supported for the parser
     *
     * @var string $output_extension_supported
     */
    public $output_extension_supported;

    /**
     * File format for Input file
     * It has values like csv, tsv, json, xml etc.
     * 
     * @var string $input_file_extension
     */
    public $input_file_extension;

    /**
     * File format for Output file
     * It has value like csv
     *
     * @var string $output_file_extension
     */
    public $output_file_extension;

    /**
     * File path for input file
     *
     * @var string $input_file_path
     */
    public $input_file_path;

    /**
     * Output file name
     *
     * @var string $output_file
     */
    public $output_file;

    /**
     * Associative mapped array of input and output headers in key value pairs
     *
     * @var array $mapped_headers
     */
    public $mapped_headers;

    public function __construct($argument_values)
    {
        ParserValidator::validateInputs($argument_values);
        $this->setExtensionFromInputs('input', $argument_values[2]);
        $this->setExtensionFromInputs('output', $argument_values[4]);
    }

    /**
     * Set file extension from inputs for input and output file name
     *
     * @param string $type
     * @param string $filename
     * 
     * @return void
     */
    private function setExtensionFromInputs($type, $filename)
    {
        $file_extension = pathinfo($filename,PATHINFO_EXTENSION);
        if ($type == 'input') {
            $this->input_file_path = $filename;
            $this->input_file_extension = $file_extension;
            
        } else {
            $this->output_file = $filename;
            $this->output_file_extension = $file_extension;
        }
    }

    /**
     * Set Input & Output Extensions supported for parser
     *
     * @param string $type
     * @param mixed $extensions
     * 
     * @return void
     */
    public function setSupportedExtensions($type, $extension)
    {
        if ($type == 'input') {
            $this->input_extensions_supported = $extension;
        } elseif(($type == 'output')) { 
            $this->output_extension_supported = $extension;
        }
    }

    /**
     * Setting Input & Output Mapped Headers associative array 
     *
     * @param array $headers
     * 
     * @return void
     */
    public function setHeaders($headers)
    {
        $this->mapped_headers = $headers;
    }

    /**
     * Reading headers array of CSV/TSV file
     *
     * @param string $file_name
     * @return void
     */
    public function readInputsFromFile($file_name)
    {
        $file_extension = pathinfo($file_name,PATHINFO_EXTENSION);
        $delimeter = $file_extension == 'csv' ? "," : "\t";
        $file_path = __DIR__ . "/..//" . $file_name;
        $handle = fopen($file_path,"r");
        $data = fgetcsv($handle, 0, $delimeter);
        return $data;
    }

    /**
     * Run Parser after all the validations
     *
     * @return void
     */
    public function run()
    {
        switch($this->input_file_extension) {
            case 'csv':
            case 'tsv':
                $input_file = __DIR__ . "/..//" . $this->input_file_path;
                $handle = fopen($input_file, "r");
                $delimeter = ($this->input_file_extension == 'csv') ? "," : "\t";
                $mapped_header = [];
                $final_result = [];

                /**
                 * Read first line from Input file
                 */
                $headers = fgetcsv($handle, 0, $delimeter,'"');
                if(!empty($headers)) {
                    foreach($headers as $column_name) {
                        array_push($mapped_header, $this->mapped_headers[$column_name]);
                    }
                }
                $output_header = $mapped_header;
                array_push($output_header, 'Count');

                $line_number = 1;
                while(($line = fgetcsv($handle, 0, $delimeter, '"'))) {
                    $line_number += 1;

                    /**
                     * If any row doesn't match with total number of columns for header
                     * it will give an exception
                     */
                    if(count($line) !== count($mapped_header)) {
                        throw new Exception("Parsing of input file failed.\nTotal count of header columns are not matching with total count of data column for line number: $line_number\n");
                    }
                    $parsed_line_array = array_combine($mapped_header, $line);

                    /**
                     * Parse and print line of input file one by one
                     */
                    print_r((object)$parsed_line_array);

                    /**
                     * Imploded with ||| so that comma seperated column value won't
                     * make an issue while writing back to csv file
                     */
                    $parsed_str = implode("|||", $parsed_line_array);

                    /**
                     * If same product with all same properties exists then increase 
                     * the count otherwise set it to 1 for new unique product
                     */
                    array_key_exists($parsed_str, $final_result) ? 
                        $final_result[$parsed_str] += 1 :
                        $final_result[$parsed_str] = 1;
                }

                /**
                 * Create the output file
                 */
                $output_file = fopen(__DIR__."/..//data/output/".$this->output_file,"w");
                $output_delimeter = ($this->output_file_extension == 'csv') ? "," : "\t";

                /**
                 * Write in Output file
                 */
                fputcsv($output_file, $output_header, $output_delimeter,'"');
                if(!empty($final_result)) {
                    foreach($final_result as $key=>$value) {
                        $line_array = explode("|||", $key);
                        array_push($line_array,$value);
                        fputcsv($output_file, $line_array, $output_delimeter,'"');
                    }
                }
                fclose($output_file);
                break;
            case 'json':
            case 'xml':
                /**
                 * Add code for XML & JSON in future here...
                */
                echo "\nNote: Currently we are not supporting JSON & XML. In future, support will be added.\n";
                break;
        }
    }
}