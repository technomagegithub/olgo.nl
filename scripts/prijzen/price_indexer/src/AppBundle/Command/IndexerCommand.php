<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Goutte\Client;

class IndexerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('Greet someone')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }

    //    $output->writeln($text);


        if(!$file = fopen("prijzen-".date('Y-m-d').".csv", "w")) {
           $output->writeln("can not open file");
           die;
        }

        $client = new Client();
        $client->getClient()->setDefaultOption('config/curl/'.CURLOPT_TIMEOUT, 60);
        $products = $this->getProducts();
        $counter = 1;
        foreach($products as $product) {

 	  $crawler = $client->request('GET', 'http://brickwat.ch/'.$product);

          $status_code = $client->getResponse()->getStatus();
          if($status_code==200){
             $result = $crawler->filter('#prices')->filter('tr.row-collapse');
             $output->writeln("Sites found for product ".$product.": ".$result->count());
             if ($result->count()) {
                for($i=0;$i<$result->count();$i++) {
                   $price   = "";
                   $company = "";
                   $values   = $result->eq($i)->filter('td');
  
                   // Get company Name
                   if($values->eq(0)->filter('a img')->count()) {
                      $company = $values->eq(0)->filter('a img')->attr('title');
                      $price = $values->eq(4)->filter('a')->text();
                   } else {
                      $company = $values->eq(0)->text();
                      $price = $values->eq(4)->text();
                   } 
                   $prices[$product][$company] = $price;
                   $companys[$company] = $company;
                } 
             }
          }
          $counter++;
          usleep(200000);
          //if ($counter > 2)
          //   break;
       }
       $this->writeResults($output, $prices, $companys, $file);
       fclose($file);
    }
    
    protected function writeResults($output, $prices, $companys, $file) {
           

        $break = ";";
        $line = "product";
        foreach ($companys as $company) {
          $line = $line.$break.$company;
        }

        fwrite($file, $line."\n"); 
	        
        foreach($prices as $product => $price) {
           $line = $product;
           foreach ($companys as $company) {
             if (isset($price[$company]))
                 $line = $line . $break . $price[$company];
             else
                 $line = $line . $break;
 
           }
           fwrite($file, $line."\n"); 
        }
    }
 
    protected function getProducts() {
       return explode("\n","10699
10700
7280
60055
70779
10677
41092
70409
75076
42031
75074
75075
10701
70792
70793
70794
70788
10693
10694
10680
42034
41085
10683
60084
31033
31034
60081
31037
41026
10668
70733
42026
60043
60086
10684
31035
41102
75080
75090
10695
10685
60007
75081
41100
60044
60074
60060
41035
21022
31010
60085
21013
75097
70795
70412
42036
21019
41037
60075
31036
75049
70808
21120
41094
31025
42037
60050
75082
60068
60093
75004
31038
31039
60079
70737
41106
75043
41095
75051
75083
75092
76038
75084
41039
42024
60047
41058
60080
70413
75094
60095
60051
42042
60097
60052
42009
42030
31313
71010
60041
60006
31017
31018
60077
60091
70782
60053
60088
10679
75072
10553
75088
10591
41091
4437
7499
60057
10506
10671
10597
10500
10592
75038
60092
41093
41104
42022
8293
8085
41105
8092
10507
21024
75056
4209
21108
10593
60009
75018
21302
10245
75052
10249
10220
75054
8095
10508
8096
10235
10232
7747
10216
10218
10243
71009
71006
71016
8129
10667
10675
10692
7281
7895
60004
60082
60078
60090
76030
76031
75079
31026
42023
42025
42029
42032
42035
42043
41101
41103
75089
75918
10197
7965
40086
9488
9491
8088
7964
31015
75030
75034
75078
10240
40123
10244
10242
10247
10248
75105
75111
41062
70404
10558
10596
10603
10623
10589
10686
70411
10586
10687
75099
75100
75101
75103
75104
7961
8087
75913
9525
42040
4644
41077
75041
76016
10602
6785
21119
31021
10246
66493
21121
41109
41108
10522");

    }
}
?>
