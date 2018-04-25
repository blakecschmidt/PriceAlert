import requests
from bs4 import BeautifulSoup
import sys

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

    print(price)

def main():
    getPrice(sys.argv[1], sys.argv[2])
main()