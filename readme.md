# Build a Supplier Product List Processor

## Requirement: 

We have multiple different formats of files that need to be parsed and returned back as a Product object with all the headings as mapped properties. 

Each product object constitutes a single row within the csv file.

### Example Application API:
`parser.php --file example_1.csv --unique-combinations=combination_count.csv`

When the above is run the parser should display row by row each product object representation of the row. And create a file with a grouped count for each unique combination i.e. make, model, colour, capacity, network, grade, condition.

### Example Product Object:
- make: 'Apple' (string, required) - Brand name
- model: 'iPhone 6s Plus' (string, required) - Model name
- colour: 'Red' (string) - Colour name
- capacity: '256GB' (string) - GB Spec name
- network: 'Unlocked' (string) - Network name
- grade: 'Grade A' (string) - Grade Name
- condition: 'Working' (string) - Condition name

### Example Unique Combinations File:
- make: 'Apple'
- model: 'iPhone 6s Plus'
- colour: 'Red'
- capacity: '256GB'
- network: 'Unlocked'
- grade: 'Grade A'
- condition: 'Working'
- count: 129

### Things to note:
  - New formats could be introduced in the future ie. (json, xml etc).
  - File headings could change in the future.
  - Some files can be very large so watch out for memory usage.
  - The code should be excutable from a terminal.
  - Please provide brief read me describing how to run your application.
  - PHP 7+ must be used.
  - Should be built using native PHP and no third party libraries.
  - Required fields if not found within file should throw an exception.


### Bonus:

  - Add unit/integration tests.

----------

## Implementation

### Parser Inputs

1. `$input_extensions` - Input format supported for parser
2. `$output_extension` - Output file format to generate 
3. `$input_output_header_mapped` - This is associative array. Its keys should match to the header of input file. Value of this array should be the header row of output file.

### Note:

- Above listed [parser inputs](#parser-inputs) can be change based on the requirements in `index.php` file.
- If input header change/add in the future, then user has to change  `$input_output_header_mapped` array in `index.php`.
- Currently Input file support CSV or TSV format. For JSON or XML, future support can be added in file `Parser.php` `run()` function.
- Output file supports:
  - CSV or
  - TSV
- To change the output file format, change `$output_extension` variable in `index.php`. By default, it's CSV.
- `testexample.csv` file is used for phpunit test. So don't delete that file from `data` folder.

### How to Run Parser

- Extract the zip file of project
- Run `composer install`
- Run command in below format to run parser: 
`php index.php --file input_file.csv --unique-combination output_file.csv`

where,

- **input_file.csv** is input CSV file. This should be uploaded to `data` folder of the project.
- **output_file.csv** is output CSV file you wanted to created. This will be generated in the `data\ouput` folder.

### PHPUnit

- Go to parent folder of the project
- Run `./vendor/bin/phpunit` in terminal to execute the PHPUnit test cases

### Edge case scenarios handled

- This parser will support the column value having comma as well. This is edge case scenario in case of CSV file, which is handled.

- If total number of column is not matching with total header columns for any row then it will give an exception and stop parsing further.

**Note:** Make sure the input and output file name will have the same file format which is mentioned in the `$input_extensions` and `$output_extension` variable of `index.php` file.

_When you cloned the project in your local repository, you will get two sample input file tab and comma seperated in data folder. If you wish, you can remove those and upload your file, pass the same file to terminal while running the parser._
