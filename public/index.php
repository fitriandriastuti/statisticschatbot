<?php
require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use \LINE\LINEBot;
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

$pass_signature = true;

// set LINE channel_access_token and channel_secret
$channel_access_token = "6WIzdc8B3o4FPzVF+OHoQmLMHvNEFqWUqcWXiKEiKzHYU6+f7/ADk4EPvMWyrETMhnqOoKe4cn8/N3reUjKD0mVkSpMFXWl3Wx3N7wfCQUY0jkYmlGKGBgv+A931pVVJibBc1NtMzF7XGw/hy9sczAdB04t89/1O/w1cDnyilFU=";
$channel_secret = "b5cebea1a4a83d42b265ff3bba9e0ae9";

// inisiasi objek bot
$httpClient = new CurlHTTPClient($channel_access_token);
$bot = new LINEBot($httpClient, ['channelSecret' => $channel_secret]);

$app = AppFactory::create();
$app->setBasePath("/public");

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello World!");
    return $response;
});

// buat route untuk webhook
$app->post('/webhook', function (Request $request, Response $response) use ($channel_secret, $bot, $httpClient, $pass_signature) {
    // get request body and line signature header
    $body = $request->getBody();
    $signature = $request->getHeaderLine('HTTP_X_LINE_SIGNATURE');

    // log body and signature
    file_put_contents('php://stderr', 'Body: ' . $body);

    if ($pass_signature === false) {
        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE?
        if (!SignatureValidator::validateSignature($body, $channel_secret, $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }
    }

// kode aplikasi nanti disini
    //@pepo
    $data = json_decode($body, true);
    if(is_array($data['events'])){
        foreach ($data['events'] as $event)
        {
            if ($event['type'] == 'message')
            {
                if($event['message']['type'] == 'text')
                {
                    // send same message as reply to user
                    //$result = $bot->replyText($event['replyToken'], $event['message']['text']);

                    //kirim sama text dari chat ditambah keterangan
//                    $result = $bot->replyText($event['replyToken'], 'Chat balasan: '.$event['message']['text']);

                    #webapi bps
                    #https://webapi.bps.go.id/v1/api/list/model/publication/domain/0000/keyword/neraca/key/0e4e501e990fd55e10da084c8f6087d5/
                    if(strtolower($event['message']['text'])=='menu'){
//                        $flexTemplate = file_get_contents("../flex_message.json"); // template flex message
//                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
//                            'replyToken' => $event['replyToken'],
//                            'messages'   => [
//                                [
//                                    'type'     => 'flex',
//                                    'altText'  => 'Test Flex Message',
//                                    'contents' => json_decode($flexTemplate)
//                                ]
//                            ],
//                        ]);
                        $message = 'Statistics Chatbot merupakan chatbot teman statistik kamu untuk mencari data statistik yang sudah dipublikasikan resmi oleh Badan Pusat Statistik. Berikut ini fitur yang tersedia pada Statistics Chatbot, silakan balas dengan reply nomor fitur:
1. Tabel Statistik
2. Publikasi
3. Indikator Strategis
4. Infografis
5. News
6. Press Release
Kembali ke menu utama dengan reply "menu"
                                    ';
                        $result = $bot->replyText($event['replyToken'], $message);
                    }elseif($event['message']['text']==1){
                        $result = $bot->replyText($event['replyToken'], 'Tabel statistik apa yang Anda cari? *ketik diawali dengan tabel keyword');
                    }elseif ($event['message']['text']==2){
                        $result = $bot->replyText($event['replyToken'], 'Publikasi statistik apa yang Anda cari? *ketik diawali dengan publikasi keyword');
                    }elseif ($event['message']['text']==3){
                        $result = $bot->replyText($event['replyToken'], 'Indikator strategis apa yang Anda cari? *ketik diawali dengan 3_');
                    }elseif ($event['message']['text']==4){
                        $result = $bot->replyText($event['replyToken'], 'Infografis apa yang Anda cari? *ketik diawali dengan 4_');
                    }elseif ($event['message']['text']==5){
                        $result = $bot->replyText($event['replyToken'], 'News statistik apa yang Anda cari? *ketik diawali dengan 5_');
                    }elseif ($event['message']['text']==6){
                        $result = $bot->replyText($event['replyToken'], 'Press release statistik apa yang Anda cari? *ketik diawali dengan 6_');
                    }

                    elseif(strtolower(substr($event['message']['text'],0,5))=='tabel'){
                        $keyword = substr($event['message']['text'], strpos($event['message']['text'], " ") + 1);
                        $key_webapibps = '0e4e501e990fd55e10da084c8f6087d5';
//                        $url = 'https://webapi.bps.go.id/v1/api/list/model/statictable/domain/0000/keyword/'.$keyword.'/key/'.$key_webapibps.'/';
                        $json = file_get_contents('https://webapi.bps.go.id/v1/api/list/model/statictable/domain/0000/keyword/'.$keyword.'/key/'.$key_webapibps.'/');
                        $obj = json_decode($json,true);
                        var_dump($obj);
                        print_r($obj);
//                        $result = $bot->replyText($event['replyToken'], 'cari tabel statistik '.$keyword.', hasilnya: '.$obj['data-availability'].', list result: '.$obj['data'][1][0]['table_id'].
//                        ', title: '.$obj['data'][1][0]['title']);

                        $flexTemplate = '{
                          "type": "bubble",
                          "header": {
                                                    "type": "box",
                            "layout": "vertical",
                            "contents": [
                              {
                                  "type": "text",
                                "text": "Hasil Tabel Statistik '.$keyword.'",
                                "weight": "bold"
                              }
                            ]
                          },
                          "body": {
                                                    "type": "box",
                            "layout": "vertical",
                            "contents": [
                              {
                                  "type": "text",
                                "text": "Berikut ini merupakan list tabel hasil pencarian",
                                "size": "sm",
                                "color": "#c9302c",
                                "weight": "bold"
                              },
                            ]
                          },
                          "footer": {
                                                    "type": "box",
                            "layout": "vertical",
                            "spacing": "sm",
                            "contents": [
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "uri",
                                  "label": "Tabel: '.$obj['data'][1][0]['title'].'",
                                  "uri": "'.$obj['data'][1][0]['excel'].'"
                                }
                              },
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "postback",
                                  "label": "2. Publikasi",
                                  "displayText": "2",
                                  "data": "2"
                                }
                              },
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "postback",
                                  "label": "3. Indikator Strategis",
                                  "displayText": "3",
                                  "data": "3"
                                }
                              },
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "postback",
                                  "label": "4. Infografis",
                                  "displayText": "4. Infografis",
                                  "data": "4. Infografis"
                                }
                              },
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "postback",
                                  "label": "5. News",
                                  "displayText": "5. News",
                                  "data": "5. News"
                                }
                              },
                              {
                                  "type": "button",
                                "style": "primary",
                                "action": {
                                  "type": "postback",
                                  "label": "6. Press Release",
                                  "displayText": "6. Press Release",
                                  "data": "6. Press Release"
                                }
                              }
                            ]
                          }
                        }  ';
                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
                            'replyToken' => $event['replyToken'],
                            'messages'   => [
                                [
                                    'type'     => 'flex',
                                    'altText'  => 'Test Flex Message',
                                    'contents' => json_decode($flexTemplate)
                                ]
                            ],
                        ]);

//                        $template = '
//                                    {
//                                      "type": "bubble",
//                                      "body": {
//                                        "type": "box",
//                                        "layout": "horizontal",
//                                        "contents": [
//                                          {
//                                            "type": "text",
//                                            "text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.",
//                                            "wrap": true
//                                          }
//                                        ]
//                                      },
//                                      "footer": {
//                                        "type": "box",
//                                        "layout": "horizontal",
//                                        "contents": [
//                                          {
//                                            "type": "button",
//                                            "style": "primary",
//                                            "action": {
//                                              "type": "uri",
//                                              "label": "Go",
//                                              "uri": "https://example.com"
//                                            }
//                                          }
//                                        ]
//                                      }
//                                    },
//                                    {
//                                      "type": "bubble",
//                                      "body": {
//                                        "type": "box",
//                                        "layout": "horizontal",
//                                        "contents": [
//                                          {
//                                            "type": "text",
//                                            "text": "Hello, World!",
//                                            "wrap": true
//                                          }
//                                        ]
//                                      },
//                                      "footer": {
//                                        "type": "box",
//                                        "layout": "horizontal",
//                                        "contents": [
//                                          {
//                                            "type": "button",
//                                            "style": "primary",
//                                            "action": {
//                                              "type": "uri",
//                                              "label": "Go",
//                                              "uri": "https://example.com"
//                                            }
//                                          }
//                                        ]
//                                      }
//                                    }
//                        ';
//                        $result = $httpClient->post(LINEBot::DEFAULT_ENDPOINT_BASE . '/v2/bot/message/reply', [
//                            'replyToken' => $event['replyToken'],
//                            'messages'   => [
//                                [
//                                    'type'     => 'flex',
//                                    'altText'  => 'Test Flex Message',
//                                    'contents' => json_decode($template)
//                                ]
//                            ],
//                        ]);

//                        $carouselTemplateBuilder = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder([
//                            new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($obj['data'][1][0]['title'],
//                                $obj['data'][1][0]['subj'],
//                                "https://statisticschatbot.herokuapp.com/statistik-chatbot.png",[
//                                new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('download xlsx',$obj['data'][1][0]['excel']),
//                            ]),
////                            new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder($obj['data'][1][1]['title'], $obj['data'][1][1]['subj'],"https://statisticschatbot.herokuapp.com/statistik-chatbot.png",[
////                                new \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('download xlsx',$obj['data'][1][1]['excel']),
////                            ]),
//                        ]);
//                        $templateMessage = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder('nama template',$carouselTemplateBuilder);
//                        $result = $bot->replyMessage($event['replyToken'], $templateMessage);

                    }

                    else{
                        $message = 'Maaf menu yang anda minta "'.$event['message']['text'].'" tidak tersedia atau salah. Kembali ke menu utama dengan reply "menu"
                                    ';
                        $result = $bot->replyText($event['replyToken'], $message);
                    }


                    // or we can use replyMessage() instead to send reply message
                    // $textMessageBuilder = new TextMessageBuilder($event['message']['text']);
                    // $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);


                    $response->getBody()->write(json_encode($result->getJSONDecodedBody()));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($result->getHTTPStatus());
                }
            }
        }
    }

});
$app->run();
