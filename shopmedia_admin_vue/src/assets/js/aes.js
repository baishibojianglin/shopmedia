import CryptoJS from '../../../node_modules/crypto-js/crypto-js.js';
const key = CryptoJS.enc.Utf8.parse("123a456b789c123d");
const iv = "1234567812345678";

//加密方法
function Encrypt(word) {
  var srcs = CryptoJS.enc.Utf8.parse(word);
  var encrypted = CryptoJS.AES.encrypt(srcs, key, {
    iv:CryptoJS.enc.Utf8.parse(iv),
    mode: CryptoJS.mode.ECB,
    padding: CryptoJS.pad.Pkcs7
  });
  return encrypted.toString();
}

//解密方法
function Decrypt(word) {
  var decrypt = CryptoJS.AES.decrypt(word, key, {
	iv:CryptoJS.enc.Utf8.parse(iv),
    mode: CryptoJS.mode.ECB,
    padding: CryptoJS.pad.Pkcs7
  });
  return CryptoJS.enc.Utf8.stringify(decrypt).toString();
}

export default{
  Decrypt,
  Encrypt
}