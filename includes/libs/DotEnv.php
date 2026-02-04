<?php
class DotEnv{
    /**
     * Convert true and false to booleans, instead of:
     *
     * VARIABLE=false -> ['VARIABLE' => 'false']
     *
     * it will be
     *
     * VARIABLE=false -> ['VARIABLE' => false]
     *
     * default = true
     */
    const PROCESS_BOOLEANS = 'PROCESS_BOOLEANS';

    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;

    /**
     * Configure the options on which the parsed will act
     *
     * @var array
     */
    protected $options = [];

    /**
     * Variables attendues dans le fichier .env
     */
    private static $requiredVariables = [
        'DB_HOST'     => 'Hôte de la base de données (ex: localhost)',
        'DB_USER'     => 'Utilisateur de la base de données (ex: root)',
        'DB_PASSWORD' => 'Mot de passe de la base de données',
        'DB_DATABASE' => 'Nom de la base de données (ex: blogart26)',
    ];

    private static $optionalVariables = [
        'DB_PORT'              => 'Port de la base de données (ex: 8889 pour MAMP)',
        'APP_DEBUG'            => 'Mode debug (true/false)',
        'APP_URL'              => 'URL de l application',
        'RECAPTCHA_SITE_KEY'   => 'Clé publique reCAPTCHA',
        'RECAPTCHA_SECRET_KEY' => 'Clé secrète reCAPTCHA',
    ];

    public function __construct(string $path, array $options = []){
        if(!file_exists($path)){
            self::showMissingEnvError($path);
        }

        $this->path = $path;

        $this->processOptions($options);
    }

    /**
     * Affiche un message d'erreur clair si le fichier .env est absent
     */
    private static function showMissingEnvError(string $path): void
    {
        $html = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Configuration manquante - BlogArt</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #e74c3c; }
        h2 { color: #333; margin-top: 25px; }
        .path { background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; color: #c7254e; }
        .code { background: #2d3748; color: #68d391; padding: 15px; border-radius: 4px; font-family: monospace; white-space: pre-wrap; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .required { color: #e74c3c; font-weight: bold; }
        .optional { color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚠️ Fichier .env manquant</h1>
        <p>Le fichier de configuration <strong>.env</strong> est introuvable à l emplacement :</p>
        <p class="path">' . htmlspecialchars($path) . '</p>
        
        <h2>📋 Comment résoudre ce problème ?</h2>
        <p>Créez un fichier <code>.env</code> à la racine du projet avec le contenu suivant :</p>
        
        <div class="code"># Configuration Base de données MAMP
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=root
DB_DATABASE=blogart26
DB_PORT=8889

# Configuration Application
APP_DEBUG=true
APP_URL=http://localhost:8888/BLOGART26

# reCAPTCHA (remplacez par vos vraies clés)
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here</div>

        <h2>📌 Variables requises</h2>
        <table>
            <tr><th>Variable</th><th>Description</th></tr>';
        
        foreach (self::$requiredVariables as $var => $desc) {
            $html .= '<tr><td class="required">' . $var . '</td><td>' . $desc . '</td></tr>';
        }
        
        $html .= '</table>

        <h2>📎 Variables optionnelles</h2>
        <table>
            <tr><th>Variable</th><th>Description</th></tr>';
        
        foreach (self::$optionalVariables as $var => $desc) {
            $html .= '<tr><td class="optional">' . $var . '</td><td>' . $desc . '</td></tr>';
        }
        
        $html .= '</table>
    </div>
</body>
</html>';
        
        die($html);
    }

    private function processOptions(array $options)
    {
        $this->options = array_merge([
            static::PROCESS_BOOLEANS => true
        ], $options);
    }

    /**
     * Processes the $path of the instances and parses the values into $_SERVER and $_ENV, also returns all the data that has been read.
     * Skips empty and commented lines.
     */
    public function load()
    {
        if(!is_readable($this->path)){
            throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if(strpos(trim($line), '#') === 0){
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = $this->processValue($value);

            if(!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)){
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    private function processValue(string $value){
        $trimmedValue = trim($value);

        if(!empty($this->options[static::PROCESS_BOOLEANS])){
            $loweredValue = strtolower($trimmedValue);

            $isBoolean = in_array($loweredValue, ['true', 'false'], true);

            if($isBoolean){
                return $loweredValue === 'true';
            }
        }
        return $trimmedValue;
    }
}
