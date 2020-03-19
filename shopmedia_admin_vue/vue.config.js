const webpack = require('webpack')
module.exports = {
    publicPath: './', //解决打包后运行显示空白页
    assetsDir:'assets' ,//静态资源目录(js,css,img,fonts)
	configureWebpack: {
	  plugins: [
		new webpack.ProvidePlugin({
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
			'window.$': 'jquery'
		})
	  ]
	}
	
}
