<?php

namespace App;

use App\Parser;
use Exception;

/**
 * This class validates all the input parameters.
 * 
 * ParserValidator will validates the inputs given by user in terminal and
 * all the static inputs provided in index.php like input/output extensions,
 * input headers and it's mapped output headers. 
 * Input file's header should also match with input headers array.
 * 
 * @copyright 2021 Sagar Shiroya
 */
class ParserValidator 
{
    /**
     * Object of Parser Class
     * It has access to all the member variables of Parser class and it's methods.
     * @var object $parser
     */
    private $parser;
    
    /**
     * Dependency Injection of Parser class
     * It sets the member variable $parser with the object of Parser class
     *
     * @param Parser $p
     */
    public function __construct(Parser $p)
    {
        $this->parser = $p;
    }

    /**
     * Check the input/output file format extension with the supported extensions
     *
     * @param mixed $supported_exts
     * @param string $file_extension
     * 
     * @return boolean  true, if file_extension matches with supported extension.
     *                  false, if it's not matches.  
     */
    public function checkExtension($supported_exts, $file_extension)
    {
        return is_array($supported_exts) ? in_array(strtolower($file_extension), $supported_exts) : (strtolower($supported_exts)==strtolower($file_extension));
    }

    /**
    * Validate Input & Output File name with supported file types
    *
    * @return void
    */
    public function fileFormatValidator()
    {
        $input_extension_supported = is_array($this->parser->input_extensions_supported)?$this->parser->input_extensions_supported: array($this->parser->input_extensions_supported);
        $output_extension_supported = $this->parser->output_extension_supported;

            if(!($this->checkExtension($input_extension_supported, $this->parser->input_file_extension))) {
                //Invalid Input format
                    throw new Exception("\nError: Input File format not supported. \nSupported formats: '" . implode(",",$input_extension_supported) . "'\n");
            }

            if(!($this->checkExtension($output_extension_supported, $this->parser->output_file_extension))) {
                //Invalid Output format
                throw new Exception("\nError: Output File format not supported. \nSupported file format: '" . $output_extension_supported . "'\n");
            }
    }

    /**
     * Validate Inputs & Outputs entered in command line
     *
     * @param int $argc_count
     * @param array $arg_values
     * 
     * @return void
     */
    public static function validateInputs($arg_values)
    {
        //Check all arguments and total number of arguments
        if($arg_values[1] !== '--file' || $arg_values[3] !== "--unique-combination" || count($arg_values) !== 5) {
            throw new Exception("Invalid Input Format. Below is the valid format:\n'php index.php --file input_file.csv --unique-combination output_file.csv'\n");
        }

        //Check whether file exists or not
        if(!file_exists($arg_values[2])) {
            throw new Exception("Input file '" . $arg_values[2] . "' not exists.\n");
        }
    }

    /**
     * Match total Input & Output headers array
     * Check Input file's header column name with Input header's array names 
     *
     * @param string $input_file
     * 
     * @return void
     */
    public function inputHeaderValidation($data)
    {
        $diff_value = array_diff($data, array_keys($this->parser->mapped_headers));
        // foreach($data as $column_name){
            if(!empty($diff_value)) {
                throw new Exception("'" . implode(",",$diff_value) . "' column not exists in Input Headers Array(\$input_file_headings) defined in index.php file.\nPlease check the array and header columns of Input file.\nAll the header names should match with each other.\n");
            }
        // }
    }
}