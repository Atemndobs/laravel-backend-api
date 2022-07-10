Convert date formatter deprecations

`IntlDateFormatter::setTimeZoneID()` and `datefmt_set_timezone_id()`
were deprecated in PHP 5.5. These were convert to
`IntlDateFormatter::setTimeZone()` and `datefmt_set_timezone()`.