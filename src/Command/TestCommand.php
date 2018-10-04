<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Navitia\Component\Service\ServiceFacade;
use Psr\Log\NullLogger;
use Doctrine\Common\Annotations\AnnotationRegistry;

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;



class TestCommand extends Command {

    protected function configure () {

        $this->setName('app:meteo');


    }

    public function execute (InputInterface $input, OutputInterface $output) {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $serializer = new Serializer($normalizers, $encoders);

        // Language of data (try your own language here!):
        $lang = 'fr';

        // Units (can be 'metric' or 'imperial' [default]):
        $units = 'metric';

        // Create OpenWeatherMap object. 
        // Don't use caching (take a look into Examples/Cache.php to see how it works).
        $owm = new OpenWeatherMap('0d15a23f17cd40f9d5631edaaf42343b');

        try {
            $weather = $owm->getWeather('Paris', $units, $lang);
            $jsonContent = $serializer->serialize($weather, 'json');

           $final = json_decode($jsonContent, true);
          
        //    var_dump($final);

          
            // var_dump($final["city"]["name"]);
            //   var_dump((int)$final["temperature"]["value"]);

        $text = 'il fait actuellement '.intval($final["temperature"]["value"]) . '°C à '. $final["city"]["name"] .'. Température min de la journée: ' . $final["temperature"]["min"]["value"] .'°C, Température max de la journée: '.$final["temperature"]["max"]["value"] . '°C. Merci @OpenWeatherMap';


        } catch(OWMException $e) {
            echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        } catch(\Exception $e) {
            echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
        }

        $output->writeln($text);
        
    }
}