import Aes from './Aes.js';

/**
 * 公共函数类
 */
const common = class{
	/**
	 * JavaScript将字典序升序排列(类似PHP中的ksort函数)
	 * @param {Object} jsonData
	 */
	jsonSort(jsonData) {
		try {
			let tempJsonObj = {};
			let sdic = Object.keys(jsonData).sort();
			sdic.map((item, index)=>{
				tempJsonObj[item] = jsonData[sdic[index]]
			})
			return tempJsonObj;
		} catch(e) {
			return jsonData;
		}
	}

	/**
	 * 原生JavaScript将json对象解析成序列化表示形式(类似jQuery的param()方法)
	 * 参考：https://www.cnblogs.com/fps2tao/p/10136721.html和https://www.jianshu.com/p/4a5c3495e05b
	 * @param {Object} json
	 */
	parseParam(json) {
		let param = '';
		Object.keys(json).map(function (key) {
			param += '&' + encodeURIComponent(key) + "=" + encodeURIComponent(json[key]);
		});
		/* Object.keys(json).map(function (key) {
			return encodeURIComponent(key) + "=" + encodeURIComponent(json[key]);
		}).join("&"); */
		return param.substr(1);
	}

	/**
	 * 生成每次请求的签名 sign 算法
	 * @param {Object} jsonObj 此处为json对象，不是json字符串
	 */
	setSign(jsonObj) {
		// 1.按字典排序
		let json = this.jsonSort(jsonObj);

		// 2.以&符号拼接字符串数据，如'key1=123&key2=abc'(类似jQuery param() 方法创建数组或对象的序列化表示形式)
		let string = this.parseParam(json);

		// 3.AES加密
		let encryptString = Aes.encode(string);
		// AES解密
		// let decryptString = Aes.decode(encryptString);console.log(decryptString);

		return encryptString;
	}

	/**
	 * 获取sign
	 */
	sign() {
		/* 生成签名 sign s */
		let get13Timestamp = (new Date()).getTime(); // 获取13位的时间戳
		let jsonObj = {"did": 'sustock2020', "version": 1, "time": get13Timestamp}; // 注意：此处为json对象，不是json字符串
		let sign = this.setSign(jsonObj); //console.log('sign', sign);
		return sign;
		/* 生成签名 sign e */
	}
	
	
	/**
	 * 根据（场馆和用户的）经纬度计算距离
	 * @param {Object} la1 纬度1
	 * @param {Object} lo1 经度1
	 * @param {Object} la2 纬度2
	 * @param {Object} lo2 经度2
	 */
	distance(la1, lo1, la2, lo2) {
		var La1 = la1 * Math.PI / 180.0;
		var La2 = la2 * Math.PI / 180.0;
		var La3 = La1 - La2;
		var Lb3 = lo1 * Math.PI / 180.0 - lo2 * Math.PI / 180.0;
		var s = 2 * Math.asin(Math.sqrt(Math.pow(Math.sin(La3 / 2), 2) + Math.cos(La1) * Math.cos(La2) * Math.pow(Math.sin(Lb3 / 2), 2)));
		s = s * 6378.137; // 地球半径 6378.137m
		s = Math.round(s * 10000) / 10000;
		return s;
	}
	
	/**
	 * 显示操作菜单
	 */
	actionSheetTap() {
		// 显示操作菜单
		uni.showActionSheet({
			// title:'标题',
			itemList: ['首页', '个人中心'],
			success: (e) => {
				let url;
				switch (e.tapIndex){
					case 0:
						url = '/pages/main/main'; // 跳转首页
						break;
					case 1:
						url = '/pages/user/user'; // 跳转个人中心
						break;
					default:
						break;
				}
				uni.switchTab({
					url: url
				})
			}
		})
	}
}

export default new common;
