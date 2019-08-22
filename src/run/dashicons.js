const https = require('https')
const fs = require('fs')

const cp_url = 'https://raw.githubusercontent.com/WordPress/dashicons/master/codepoints.json';
const scss_path = './src/scss/variables/_dashicons.scss';
let data = '';

https.get(cp_url, res => {
	res.on('data', d => {
		data += d;
	});
	res.on('end',() => {
		let cp = JSON.parse(data);
		let scss = `/* WordPress Dashicons Vars */
/* generated from ${cp_url} */

`;
		Object.keys(cp).forEach( k => {
			let v = cp[k].toString(16);
			scss += `$dashicon-${k}: '\\${v}';
`;
});
		fs.writeFileSync(scss_path,scss);
		console.log(`Saved in ${scss_path}`);
	});
});
