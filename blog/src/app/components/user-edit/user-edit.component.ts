import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Global } from '../../services/global';

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})
export class UserEditComponent implements OnInit {

	public page_title: string;
	public user: User;
	public identity;
	public token;
	public status: string;
	public message: string;
	public url;

  public froala_options: Object = {
    charCounterCount: true,
    languaje: 'es',
  }

	public afuConfig = {
	    multiple: false,
	    formatsAllowed: ".jpg, .png, .gif, .jpeg",
	    maxSize: "1",
	    uploadAPI:  {
	      url: Global.url+'user/upload',
	      headers: {
	     	"Authorization" : this._userService.getToken()
	      }
	    },
	    theme: "attachPin",
	    hideProgressBar: false,
	    hideResetBtn: true,
	    hideSelectBtn: false,
	    attachPinText: 'Choose your avatar'
	};

  	constructor(
  		private _userService: UserService
  	) {

  		this.page_title = 'User settings';
  		this.user = new User(1, '', '', 'ROLE_USER', '', '', '', '');

  		this.identity = this._userService.getIdentity();
  		this.token = this._userService.getToken();

  		this.user = new User(
  			this.identity.sub, 
  			this.identity.name, 
  			this.identity.surname, 
  			this.identity.role, 
  			this.identity.email, 
  			'', 
  			this.identity.description,
  			this.identity.image);

  		this.url = Global.url;

  	}

  	ngOnInit() {
  	}

  	onSubmit(form) {

  		this._userService.update(this.token, this.user).subscribe(
  			response => {
  				console.log(response);
  				if(response && response.status) {

  					this.status = 'success';
  					this.message = response.message;

  					if(response.changes.name) {

  						this.user.name = response.changes.name;

  					}

  					if(response.changes.surname) {

  						this.user.surname = response.changes.surname;

  					}

  					if(response.changes.email) {

  						this.user.email = response.changes.email;

  					}

  					if(response.changes.description) {

  						this.user.description = response.changes.description;

  					}

  					if(response.changes.image) {

  						this.user.image = response.changes.image;

  					}

  					this.identity = this.user;
  					localStorage.setItem('identity', JSON.stringify(this.identity));

  				} else {

  					this.status = 'error';
  					this.message = response.message;

  				}

  			},
  			error => {

  				this.status = 'error'
  				this.message = error.error.message;
  				console.log(<any>error);
  			
  			}

  		);

  	}

  	avatarUpload(e) {

  		let data = JSON.parse(e.response);

  		this.user.image = data.image;

  	}

}
