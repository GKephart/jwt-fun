import {Component, OnInit} from "@angular/core";
import {SessionService} from "./services/session.service";


@Component({
	selector: "angular4-example",
	templateUrl: "./templates/angular4-example-app.html"
})

export class AppComponent implements OnInit{

	constructor(private sessionService: SessionService){}

	ngOnInit() : void {
		this.sessionService.setSession()
			.subscribe(response=> response);
	}


}