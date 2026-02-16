/**
 * Test automatisé : Vérification de la taille du logo responsive
 *
 * Ce script vérifie que le logo s'affiche avec les bonnes dimensions
 * selon les breakpoints définis.
 */

const viewports = [
	{ name: 'Mobile', width: 375, height: 667, expectedHeight: 48 },
	{ name: 'Tablet', width: 768, height: 1024, expectedHeight: 70 },
	{ name: 'Desktop', width: 1280, height: 800, expectedHeight: 110 }
];

async function testLogoSizes() {
	const puppeteer = require('puppeteer');

	console.log('🚀 Démarrage des tests automatisés du logo...\n');

	const browser = await puppeteer.launch({
		headless: 'new',
		args: ['--no-sandbox', '--disable-setuid-sandbox']
	});

	try {
		for (const viewport of viewports) {
			const page = await browser.newPage();

			// Configurer le viewport
			await page.setViewport({
				width: viewport.width,
				height: viewport.height
			});

			// Charger la page
			console.log(`📱 Test ${viewport.name} (${viewport.width}x${viewport.height})...`);
			await page.goto('http://127.0.0.1:8000', {
				waitUntil: 'networkidle0'
			});

			// Attendre que le logo soit chargé
			await page.waitForSelector('.site-header__logo img', { timeout: 5000 });

			// Récupérer les dimensions calculées du logo
			const logoInfo = await page.evaluate(() => {
				const img = document.querySelector('.site-header__logo img');
				if (!img) return null;

				const computedStyle = window.getComputedStyle(img);
				const rect = img.getBoundingClientRect();

				return {
					computedHeight: computedStyle.height,
					actualHeight: rect.height,
					computedWidth: computedStyle.width,
					actualWidth: rect.width,
					htmlHeight: img.getAttribute('height'),
					htmlWidth: img.getAttribute('width'),
					src: img.src
				};
			});

			if (!logoInfo) {
				console.log(`   ❌ Logo non trouvé!\n`);
				continue;
			}

			const actualHeight = Math.round(logoInfo.actualHeight);
			const isCorrect = actualHeight === viewport.expectedHeight;
			const icon = isCorrect ? '✅' : '❌';

			console.log(`   ${icon} Hauteur attendue: ${viewport.expectedHeight}px`);
			console.log(`   ${icon} Hauteur réelle: ${actualHeight}px`);
			console.log(`   📏 CSS height: ${logoInfo.computedHeight}`);
			console.log(`   📏 HTML height attr: ${logoInfo.htmlHeight}`);
			console.log(`   📏 Largeur réelle: ${Math.round(logoInfo.actualWidth)}px`);

			if (!isCorrect) {
				console.log(`   ⚠️  ÉCHEC: Différence de ${Math.abs(actualHeight - viewport.expectedHeight)}px`);
			}

			console.log('');

			await page.close();
		}

		console.log('✅ Tests terminés');

	} catch (error) {
		console.error('❌ Erreur lors des tests:', error.message);
	} finally {
		await browser.close();
	}
}

// Vérifier si puppeteer est installé
try {
	require.resolve('puppeteer');
	testLogoSizes();
} catch (e) {
	console.log('❌ Puppeteer n\'est pas installé.');
	console.log('📦 Installation: npm install puppeteer');
	console.log('\nAlternativement, testez manuellement:');
	console.log('1. Ouvrez http://127.0.0.1:8000 dans Chrome');
	console.log('2. Ouvrez DevTools (F12) > Elements');
	console.log('3. Inspectez le logo (.site-header__logo img)');
	console.log('4. Dans l\'onglet Computed, vérifiez "height"');
	console.log('5. Redimensionnez la fenêtre et observez les changements');
}
