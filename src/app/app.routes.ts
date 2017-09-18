import {RouterModule, Routes} from "@angular/router";
import {BaconComponent} from "./components/bacon-component";
import {HomeComponent} from "./components/home-component";
import {SessionService} from "./services/session.service";
import {BaconService} from "./services/bacon-service";
import {CookieService} from "ngx-cookie";
//import {Status} from "./classes/status";

export const allAppComponents = [BaconComponent, HomeComponent];

export const routes: Routes = [
	{path: "bacon", component: BaconComponent},
	{path: "", component: HomeComponent}
];

export const appRoutingProviders: any[] = [BaconService,CookieService, SessionService];

export const routing = RouterModule.forRoot(routes);