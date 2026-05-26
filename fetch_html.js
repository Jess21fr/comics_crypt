const { chromium } = require('playwright');

(async () => {
    const url = process.argv[2];

    const browser = await chromium.launch({
        headless: false,
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
        await page.goto(url, { timeout: 120000, waitUntil: 'domcontentloaded' });

        console.log("Attente de la fin du challenge Cloudflare…");

        // On attend que Cloudflare disparaisse et que la vraie page apparaisse
        await page.waitForSelector("table.listing, a[href*='/series/']", {
            timeout: 60000
        });

        console.log("Cloudflare OK, page réelle chargée !");
        console.log("URL réelle :", page.url());

        const html = await page.content();
        console.log(html);

    } catch (err) {
        console.error("Erreur Playwright :", err);
    }

    await browser.close();
})();
