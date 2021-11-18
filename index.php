<?php

require_once 'src/bootstrap.php';

use App\Parser;
use App\ParserValidator;

/**
 * @var array $input_extensions supported file types for input file
 * @var array $output_extension Output file type
 * @var array $input_output_header_mapped Header array for Input-Output mapping
 */
$input_extensions = ['csv', 'tsv', 'json', 'xml'];
$output_extension = 'csv';
$input_output_header_mapped = [
    "brand_name" => "Make",
    "model_name" => "Model",
    "condition_name" => "Condition",
    "grade_name" => "Grade",
    "gb_spec_name" => "Capacity",
    "colour_name" => "Colour",
    "network_name" => "Network"
];

$start_time = microtime(true);

try {
    /**
     * Set all the member variables for parser for future use
     */
    $parser = new Parser($argv);
    $parser->setSupportedExtensions('input', $input_extensions);
    $parser->setSupportedExtensions('output', $output_extension);
    $parser->setHeaders($input_output_header_mapped);

    /**
    * Validates all the inputs entered by user from terminal
    */
    $validator = new ParserValidator($parser);
    $validator->fileFormatValidator();

    $input_headers = $parser->readInputsFromFile($argv[2]);
    if(!empty($input_headers)){
        $validator->inputHeaderValidation($input_headers);
    }

    /**
     * Run the parser after successful validation
     */
    $parser->run();
} catch (Exception $e) {
    echo "\nError: " . $e->getMessage();
    exit;
}

/**
 * End clock time in seconds
 */ 
$end_time = microtime(true);


/**
 * Calculate script execution time
 */
$execution_time = ($end_time - $start_time);
echo "Input file parsed in ".round($execution_time, 2)." seconds.\n";
echo "Output file generated in `data\output` folder\n";
