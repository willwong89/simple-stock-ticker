<?php
/**
* Plugin Name: Simple Stock Ticker
* Plugin URI: http://willwong.info
* Description: This plugin creates a shortcode to output the current price for a single stock ticker symbol
* Version: 1.0
* Author: William Wong
* Author URI: http://willwong.info
*/
function create_stock_ticker( $atts ){
    $param = shortcode_atts( array(
        's' => '',
        'name' => true,
        'symbol' => true,
        'price' => true,
        'change' => true,
        'color' => true,
    ), $atts );
    
    $symbol = $param['s'];
    $displayName = filter_var( $param['name'], FILTER_VALIDATE_BOOLEAN );
    $displaySymbol = filter_var( $param['symbol'], FILTER_VALIDATE_BOOLEAN );
    $displayPrice = filter_var( $param['price'], FILTER_VALIDATE_BOOLEAN );
    $displayChange = filter_var( $param['change'], FILTER_VALIDATE_BOOLEAN );
    $color = filter_var( $param['color'], FILTER_VALIDATE_BOOLEAN );
    
    
    $csv = readCSV('http://download.finance.yahoo.com/d/quotes.csv?s='.$symbol.'&f=nsl1c1&e=.csv');
    $data = $csv[0]; /* data[Name, Symbol, LastTradePriceOnly, Change] */
    
    // if the query result is N/A, break 
    if($data[0] == 'N/A') return '<span style="text-decoration:line-through;">'.$data[1].' is not a valid symbol.</span>'; 
    
    $increase = NULL; // to determine the price change
    if($data[3][0] == '+')  $increase = true;  
    if($data[3][0] == '-')  $increase = false;  
    
    $output = '<span>';
    if($color){
        if($increase)     $output = '<span style="color:green">';
        if($increase === false)     $output = '<span style="color:red">';
    }
    
    if($displayName && $displaySymbol){
        $output .= ($data[0] . '(' . $data[1] . ') ');
    }
    else{
        if($displayName)   $output .= $data[0] .' ';
        if($displaySymbol) $output .= $data[1] .' ';
    }
    
    if($displayChange){
        if($data[3][0] == '+')  $output .= '&#9650; ';  
        if($data[3][0] == '-')  $output .= '&#9660; ';  
    }
    if($displayPrice)   $output .= $data[2];
    
    $output .= '</span>';
    
    return $output;
    
}//end create_stock_ticker()

function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
	while (!feof($file_handle) )  $line_of_text[] = fgetcsv($file_handle, 1024);
	fclose($file_handle);
	return $line_of_text;
}//end readCSV

add_shortcode('stock-ticker', 'create_stock_ticker');
?>