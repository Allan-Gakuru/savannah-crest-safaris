[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
$php = (Get-Command php -ErrorAction Stop).Source
$sources = @(
    (Join-Path $root 'wp-content/themes/savannah-crest'),
    (Join-Path $root 'wp-content/plugins/scs-core'),
    $PSScriptRoot
)
$files = @(Get-ChildItem -LiteralPath $sources -Recurse -File -Filter '*.php' | Sort-Object FullName)
if ($files.Count -eq 0) {
    throw 'No project PHP files found.'
}
foreach ($file in $files) {
    & $php -l $file.FullName
    if ($LASTEXITCODE -ne 0) {
        throw "PHP syntax error in $($file.FullName)"
    }
}
Write-Output "PHP lint passed for $($files.Count) files."
