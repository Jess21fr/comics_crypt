const { chromium } = require('playwright');

(async () => {
    const url = process.argv[2];

    // IMPORTANT : Chromium doit être visible pour que Cloudflare laisse passer
    const browser = await chromium.launch({
        headless: false,   // fenêtre visible obligatoire
        args: [
            '--disable-blink-features=AutomationControlled',
            '--disable-web-security',
            '--disable-features=IsolateOrigins,site-per-process',
            '--no-sandbox',
            '--disable-setuid-sandbox'
        ]
    });

    const context = await browser.newContext({
        userAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124 Safari/537.36",
        viewport: { width: 1400, height: 900 }
    });

    const page = await context.newPage();

    console.log("Chargement de la page…");

    try {
        // On attend longtemps, Cloudflare peut être lent
        await page.goto(url, { timeout: 120000, waitUntil: 'domcontentloaded' });

        // On laisse Cloudflare respirer
        await page.waitForTimeout(6000);

        const html = await page.content();
        console.log(html);

    } catch (err) {
        console.error("Erreur Playwright :", err);
    }

    await browser.close();
})();
