param(
  [string]$HostName = "localhost",
  [int]$Port = 8000
)

php -S "$HostName`:$Port" -t public
