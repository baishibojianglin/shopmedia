import CryptoJS from 'crypto-js';

/**
 * AES加密、解密类
 */
let key = CryptoJS.enc.Latin1.parse('63c1e23f192033ceec6f883639591b76'); // 密钥
let iv = CryptoJS.enc.Latin1.parse('1234567812345678'); // 初始化向量
const Aes = class {
	/**
	 * AES加密
	 * @param {Object} str
	 */
	encode(str) {
        return CryptoJS.AES.encrypt(str, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC
            //,padding: CryptoJS.pad.ZeroPadding
        }).toString();
    }

	/**
	 * AES解密
	 * @param {Object} str
	 */
    decode(str) {
        return CryptoJS.AES.decrypt(str, key, {
            iv: iv,
            mode: CryptoJS.mode.CBC
            //,padding: CryptoJS.pad.ZeroPadding
        }).toString(CryptoJS.enc.Utf8);
    }
}

export default new Aes;
