#!/usr/bin/python3

import os
import json
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
import requests
from bs4 import BeautifulSoup

def sendEmail(product, site, price, url, email_info):

    try:
        s = smtplib.SMTP(email_info['smtp_url'])
        s.starttls()
        s.login(email_info['user'], email_info['password'])
    except smtplib.SMTPAuthenticationError:
        print('Failed to login')
    else:
        print('Logged in! Composing message..')
        msg = MIMEMultipart('alternative')
        msg['Subject'] = 'Price Alert - %s' % price
        msg['From'] = email_info['user']
        msg['To'] = email_info['user']
        text = 'The price for %s on %s is currently %s !! URL to salepage: %s' % (product, site,
            price, url)
        part = MIMEText(text, 'plain')
        msg.attach(part)
        s.sendmail(email_info['user'], email_info['user'], msg.as_string())
        print('Message has been sent.')

def getPrice(url, site):

    r = requests.get(url, headers={
        'User-Agent':
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
    })
    r.raise_for_status()

    soup = BeautifulSoup(r.text, 'html.parser')

    if site == "amazon":
        soup = soup.find(id="priceblock_ourprice")

        priceElements = []
        for tag in soup:
            tag = tag.string.strip()
            if not tag.isdigit():
                continue
            priceElements.append(tag)

        price = float(priceElements[0] + "." + priceElements[1])

    elif site == "bestbuy":
        soup = soup.find(attrs={"class": "pb-purchase-price"})
        soup = soup.contents[0].contents
        price = float(soup[2])

    elif site == "dell":
        soup = soup.find(attrs={"data-testid": "sharedPSPDellPrice"})
        price = float(soup.string.strip().replace("$", "").replace(",", ""))

    elif site == "walmart":
        soup = soup.find(attrs={"class": "Price-group"})
        priceElements = []

        for tag in soup:
            tag = tag.string.strip()
            priceElements.append(tag)

        price = float(priceElements[1] + priceElements[2] + priceElements[3])

    elif site == "target":
        soup = soup.find(attrs={"data-test": "product-price"})
        soup = soup.contents[0].contents[0].replace("$", "")
        price = float(soup)

    return price

def getConfig(config):
    with open(config, 'r') as f:
        return json.loads(f.read())


def main():

    print(getPrice("https://www.bestbuy.com/site/nintendo-switch-32gb-console-neon-red-neon-blue-joy-con/5670100.p?skuId=5670100", "bestbuy"))

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
