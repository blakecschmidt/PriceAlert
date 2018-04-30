#!/usr/bin/python3

import os
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
import requests
from bs4 import BeautifulSoup

def sendEmail(itemName, retailers, prices, urls, senderEmail, destEmail):

    try:
        s = smtplib.SMTP(senderEmail[2])
        s.starttls()
        s.login(senderEmail[0], senderEmail[1])
    except smtplib.SMTPAuthenticationError:
        print('Failed to login')
    else:
        print('Logged in! Composing message..')
        msg = MIMEMultipart('alternative')
        msg['Subject'] = "Price Alert Notification"
        msg['From'] = senderEmail[0]
        msg['To'] = destEmail

        msgText = "We have detected that the price of one of your items has dropped below your Price Alert threshold on one or more sites!\n\nItem Name: " + itemName + "\n\n"

        for idx in range(0, len(retailers)):
            msgText += "Site: " + retailers[idx] + "\nPrice: " + prices[idx] + "\nURL: " + urls[idx] + "\n\n"

        msgText += "Thanks for using Price Alert!"
        part = MIMEText(msgText, 'plain')
        msg.attach(part)
        s.sendmail(senderEmail[0], destEmail, msg.as_string())
        print('Message has been sent.')

def getPrice(url, site):

    r = requests.get(url, headers={
        'User-Agent':
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
    })
    r.raise_for_status()

    soup = BeautifulSoup(r.text, 'html.parser')

    if site == "Amazon":
        soup = soup.find(id="priceblock_ourprice")

        priceElements = []
        for tag in soup:
            tag = tag.string.strip()
            if not tag.isdigit():
                continue
            priceElements.append(tag)

        price = float(priceElements[0] + "." + priceElements[1])

    elif site == "Best Buy":
        soup = soup.find(attrs={"class": "pb-purchase-price"})
        soup = soup.contents[0].contents
        price = float(soup[2])

    elif site == "Dell":
        soup = soup.find(attrs={"data-testid": "sharedPSPDellPrice"})
        price = float(soup.string.strip().replace("$", "").replace(",", ""))

    elif site == "Walmart":
        soup = soup.find(attrs={"class": "Price-group"})
        priceElements = []

        for tag in soup:
            tag = tag.string.strip()
            priceElements.append(tag)

        price = float(priceElements[1] + priceElements[2] + priceElements[3])

    elif site == "Target":
        soup = soup.find(attrs={"data-test": "product-price"})
        soup = soup.contents[0].contents[0].replace("$", "")
        price = float(soup)

    else:
        price = None

    return price

def main():

    #print(getPrice("https://www.bestbuy.com/site/nintendo-switch-32gb-console-neon-red-neon-blue-joy-con/5670100.p?skuId=5670100", "Best Buy"))

    sendEmail("Xbox", ["Amazon", "Dell", "Best Buy"], ["400.00", "425.23", "500"], ["https://www.amazon.com/Xbox-One-X-1TB-Console/dp/B074WPGYRF/ref=sr_1_3?s=videogames&ie=UTF8&qid=1524680613&sr=1-3&keywords=xbox+one+x", "url", "url"], ["pricealertnotify@gmail.com", "3qKL^yoc*,Aq6ZmH$rDn", "smtp.gmail.com:587"], "blakecschmidt@gmail.com")
    '''sleepTime = 43200
    config = getConfig('%s/config.json' % os.path.dirname(os.path.realpath(__file__)))
    items = config['items']
    base_urls = config['base_url']
    xpath_selectors = config['xpath_selector']

    while True:
        for idx in range(0, len(base_urls)):
            print("Checking price for the %s on %s (should be lower than %s)" % (items[idx][0], items[idx][1], items[idx][3]))
            end_url = items[idx][2].replace("'", '%27')
            end_url = end_url.replace("(", '%28')
            end_url = end_url.replace(")", '%29')
            item_page = base_urls[idx] + end_url
            price = get_price(item_page, xpath_selectors[idx])
            product = items[idx][0]
            site = items[idx][2]
            if not price:
                print("\n")
                continue
            elif price <= items[idx][3]:
                print('Price is %s!! Trying to send email.' % price)
                sendEmail(product, site, price, item_page, config['email'])
            else:
                print('Price is %s. Ignoring...' % price)
            print("\n")

        print('Sleeping for %d seconds' % sleepTime)
        time.sleep(sleepTime)'''

main()
