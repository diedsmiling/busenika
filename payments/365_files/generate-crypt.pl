#!/usr/bin/perl

###################################################################
### 365 Billing Generate Encryption String Utility              ###
###                                                             ###
### USAGE: ./generate-crypt.pl 'message' 'key'                  ###
###                                                             ###
### message should contain a string with URL Encoded name value ###
### pairs for "Amount", "ProductDescription" and "Template"     ###
###                                                             ###
### Example:                                                    ###
### "Amount=1.23&ProductDescription=Test+123&Template=checkout" ###
###                                                             ###
### key should contain the encryption key assigned located in   ###
### your website settings in http://reports.365billing.com      ###
###                                                             ###
###                                                             ###
###################################################################

# Arguments
# ARGV[0] = URL Encoded Name Value pairs string
# ARGV[1] = Encryption Key

use lib ".";
use Crypt365;

# if two arguments are not passed in, print usage message
print "\nUSAGE: ./generate-crypt.pl 'message' 'key'\n\n" unless $ARGV [1];

# call encrypt_b64 (base 64 encoding) method from Crypt365.pm module
# passing in string to encrypt and encryption key
$encrypted_string = Crypt365::encrypt_b64($ARGV[0], $ARGV[1]);

# printing out newly encrypted string
print $encrypted_string;