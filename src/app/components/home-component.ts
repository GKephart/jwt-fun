import {Component} from "@angular/core";
import {CookieService} from "ngx-cookie";

@Component({
	templateUrl: "./templates/home.html"
})

export class HomeComponent {
	cookieJar : any =[];

	constructor( public cookieService : CookieService) {
		this.cookieJar = this.caughtInTheCookieJar();
	}

	caughtInTheCookieJar() : any {
		return this.cookieService.getAll()

	}

}