<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//阿里云oss对象存储
if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

//对象存储
use OSS\OssClient;
use OSS\Core\OssException;

//短信
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


/**
 * 阿里短信接口
 * @param $phone
 * @param $verifyCode
 * @throws ClientException
 */
function sendSms($phone, $verifyCode){
    /*设置短信验证码*/
    AlibabaCloud::accessKeyClient(config('aliyun.accessKeyId'), config('aliyun.accessKeySecret'))->regionId(config('aliyun.regionId'))->asDefaultClient();
    try {
        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            // ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => config('aliyun.regionId'),
                    'PhoneNumbers' => $phone,
                    'SignName' => config('aliyun.signName'),
                    'TemplateCode' => config('aliyun.templateCode'),
                    'TemplateParam' => "{\"code\":\"".$verifyCode."\"}",
                ],
            ])
            ->request();
        //print_r($result->toArray());
        //return json($informationcode);
    } catch (ClientException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    } catch (ServerException $e) {
        echo $e->getErrorMessage() . PHP_EOL;
    }
}

/**
 * 上传图片
 * @param $info
 */
function uploadimage($info)
{
    // 阿里云RAM账号AccessKey
    $accessKeyId = config('aliyun.accessKeyId');
    $accessKeySecret = config('aliyun.accessKeySecret');
    // Endpoint以成都为例，其它Region请按实际情况填写。
    $endpoint = config('aliyun.endpoint');
    // 存储空间名称
    $bucket = config('aliyun.bucket');
    // 文件名称
    $object = md5(uniqid(mt_rand(),true)).$info['name'];
    // <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt
    $filePath = $info['tmp_name'];

    try{
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result=$ossClient->uploadFile($bucket, $object, $filePath);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }

    //返回图片地址
    $data['name']=$object;
    $data['url']=$result['info']['url'];
    return $data;
}

/**
 * 删除图片
 * @param $data
 */
function deleteimage($data)
{
    // 阿里云RAM账号AccessKey
    $accessKeyId = config('aliyun.accessKeyId');
    $accessKeySecret = config('aliyun.accessKeySecret');
    // Endpoint以成都为例，其它Region请按实际情况填写。
    $endpoint = config('aliyun.endpoint');
    // 存储空间名称
    $bucket = config('aliyun.bucket');
    // 文件名称
    $object =$data['name'];

    try{
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->deleteObject($bucket, $object);
    } catch(OssException $e) {
        printf(__FUNCTION__ . ": FAILED\n");
        printf($e->getMessage() . "\n");
        return;
    }
    //print(__FUNCTION__ . ": OK" . "\n");
}

/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param array $data 数据
 * @param int $httpCode http状态码
 * @return \think\response\Json
 */
function show($status, $message, $data = [], $httpCode = 200){
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];
    return json($data, $httpCode);
}

/**
 * 生成唯一带前缀的随机数
 * @param string $pre 随机数前缀
 * @param $min
 * @param $max
 * @param array|int $number 用于比较的值
 * @return string
 */
function uniqueRand($pre = '', $min, $max, $number){
    // 生成带前缀的随机数
    $rand = $pre . mt_rand($min, $max);

    // 判断生成的随机数是否唯一，重复时重新生成
    if (is_array($number)) {
        $flag = in_array($rand, $number) ? false : true;
    } else {
        $flag = $rand == $number ? false : true;
    }
    if ($flag == false) {
        uniqueRand($pre, $min, $max, $number);
    } else {
        return $rand;
    }
}

/**
 * 根据两点的经纬度计算距离
 * @param $lat1
 * @param $lon1
 * @param $lat2
 * @param $lon2
 * @param string $unit
 * @return float
 */
function distance($lat1, $lon1, $lat2, $lon2, $unit = "K"){
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else { //mi
        return $miles;
    }
}

/**
 * 导出Excel 方法一
 * @param $strTable	表格内容
 * @param $filename 文件名
 */
function downloadExcel($strTable, $filename)
{
    header("Content-type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=".$filename."_".date('Y-m-d').".xls");
    header('Expires:0');
    header('Pragma:public');
    echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.$strTable.'</html>';
}

/**
 * 导出Excel 方法二：PHPExcel
 * @param $expTitle 导出文件名
 * @param $expCellName 导出文件列名称
 * @param $expTableData 导出数据
 * @throws PHPExcel_Exception
 * @throws PHPExcel_Reader_Exception
 */
function exportExcel($expTitle, $expCellName, $expTableData)
{
    $xlsTitle = iconv('utf-8', 'gb2312', $expTitle); // 文件名称
    $fileName = $xlsTitle . date('_YmdHis'); // or $xlsTitle 文件名称可根据自己情况设定
    $cellNum = count($expCellName);
    $dataNum = count($expTableData);
    vendor('phpoffice.phpexcel.Classes.PHPExcel'); // 引入PHPExcel类，或 Loader::import('phpoffice.phpexcel.Classes.PHPExcel', VENDOR_PATH, '.php');
    $objPHPExcel = new \PHPExcel(); // 方法一
    $cellName = array('A','B', 'C','D', 'E', 'F','G','H','I', 'J', 'K','L','M', 'N', 'O', 'P', 'Q','R','S', 'T','U','V', 'W', 'X','Y', 'Z', 'AA',
        'AB', 'AC','AD','AE', 'AF','AG','AH','AI', 'AJ', 'AK', 'AL','AM','AN','AO','AP','AQ','AR', 'AS', 'AT','AU', 'AV','AW', 'AX',
        'AY', 'AZ');
    // 设置头部导出时间备注
    $objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1'); // 合并单元格
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . ' 导出时间:' . date('Y-m-d H:i:s'));
    // 设置列名称
    for ($i = 0; $i < $cellNum; $i++) {
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);
    }
    // 赋值
    for ($i = 0; $i < $dataNum; $i++) {
        for ($j = 0; $j < $cellNum; $j++) {
            $objPHPExcel->getActiveSheet(0)->setCellValue(
                $cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]
            );
        }
    }
    ob_end_clean(); // 这一步非常关键，用来清除缓冲区防止导出的excel乱码
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
    header("Content-Disposition:attachment;filename=$fileName.xls"); // "xls"参考下一条备注
    $objWriter = \PHPExcel_IOFactory::createWriter(
        $objPHPExcel, 'Excel2007'
    ); // "Excel2007"生成2007版本的xlsx，"Excel5"生成2003版本的xls
    $objWriter->save(RUNTIME_PATH . $fileName . '.xlsx'); // 下载到服务器
    // TODO：下载到本地
    //$objWriter->save('php://output');
}