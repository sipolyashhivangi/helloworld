package FlexUnittesting;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.firefox.FirefoxDriver;

public class AdvisorRegistration {

	public static void main(String[] args) throws InterruptedException {
		
		WebDriver driver=new FirefoxDriver();
		driver.get("https://flextestuser:NS3CT4bL@dev.flexscore.com/test/");
		driver.manage().window().maximize();
	    driver.findElement(By.id("signinPopupButton")).click();
	    driver.findElement(By.id("signupadvisortab")).click();
	    driver.findElement(By.id("account-link")).click();
	    driver.findElement(By.id("advsignemail")).clear();
	    driver.findElement(By.id("advsignemail")).sendKeys("ayushmrbk@yahoo.com");
	    driver.findElement(By.id("advsignpassword1")).clear();
	    driver.findElement(By.id("advsignpassword1")).sendKeys("ranjan123");
	    driver.findElement(By.id("advsignpassword2")).clear();
	    driver.findElement(By.id("advsignpassword2")).sendKeys("ranjan123");
	    driver.findElement(By.id("termscheckadv")).click();
	    driver.findElement(By.id("createadvisor")).click();
	    driver.findElement(By.xpath("//div[@id='comparisonContents']/a")).click();
	    Thread.sleep(8000);
	    driver.quit();
		
	}

	}


