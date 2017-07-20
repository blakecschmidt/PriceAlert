#!/usr/bin/python

import os
import re
import json
import time
import requests
import smtplib
from lxml import html
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

class Product:

    smtp_url = "smtp.gmail.com:587"
    user = "blakecschmidt@gmail.com"
    password = "rfoqxeyfhgknrjlz"

    def __init__(self, name, price):
        self.name = name
        self.price = price
        self.sleepTime = 43200
        self.siteNames = []
        self.baseURLs = []
        self.endURLs = []
        self.xpathSelectors = []

    def setPrice(self, price):
        set.price = price

    def setSleepTime(self, sleepTime):
        self.sleepTime = sleepTime

    def changeName(self, name):
        self.name = name

    def getSize(self):
        return len(self.siteNames)

    def addSite(self, siteName, baseURL, endURL, xpathSelector):
        self.addSiteName(siteName)
        self.addBaseURL(baseURL)
        self.addEndURL(endURL)
        self.addXpathSelector(xpathSelector)

    def addSiteName(self, siteName):
        self.siteNames.append(siteName)

    def addBaseURL(self, baseURL):
        self.baseURLs.append(baseURL)

    def addEndURL(self, endURL):
        self.endURLs.append(endURL)

    def addXpathSelector(self, xpathSelector):
        self.xpathSelectors.append(xpathSelector)

    def deleteSite(self, siteName):
        idx = self.siteNames.index(siteName)
        self.deleteSiteName(idx)
        self.deleteBaseURL(idx)
        self.deleteEndURL(idx)
        self.deleteXpathSelector(idx)

    def deleteSiteName(self, idx):
        del self.siteNames[idx]

    def deleteBaseURL(self, idx):
        del self.baseURLs[idx]

    def deleteEndURL(self, idx):
        del self.endURLs[idx]

    def deleteXpathSelector(self, idx):
        del self.xpathSelectors[idx]


def send_email(product, site, price, url, email_info):
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


def get_price(url, selector):
    r = requests.get(url, headers={
        'User-Agent':
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36'
    })

    r.raise_for_status()
    tree = html.fromstring(r.text)

    try:
        # extract the price from the string
        if selector[-1] == "]":
            price_string = re.findall('\d+.\d+', tree.xpath(selector)[0].text)[0]
        else:
            price_string = re.findall('\d+.\d+', tree.xpath(selector)[0])[0]

        print('\033[1m' + price_string + '\033[0m')
        return float(price_string.replace(",", ""))

    except (IndexError, TypeError):
        print('\033[91m' + "ERROR:" + '\033[0m' + " Didn\'t find the \'price\' element, trying again later...")


def get_config(config):
    with open(config, 'r') as f:
        return json.loads(f.read())


def main():
    sleepTime = 43200
    config = get_config('%s/config.json' % os.path.dirname(os.path.realpath(__file__)))
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
                send_email(product, site, price, item_page, config['email'])
            else:
                print('Price is %s. Ignoring...' % price)
            print("\n")

        print('Sleeping for %d seconds' % sleepTime)
        time.sleep(sleepTime)

main()
