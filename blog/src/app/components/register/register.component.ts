import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [UserService]
})
export class RegisterComponent implements OnInit {

  public page_title: string;
  public user: User;
  public status: string;
  public message: string;

  constructor(
    private _userService: UserService
  ) {

    this.page_title = "Check in";
    this.user = new User(1, '', '', 'ROLE_USER', '', '', '', '');

  }

  ngOnInit() {

    console.log("Component of register");

  }

  onSubmit(form) {

    this._userService.register(this.user).subscribe(
      response => {

        if(response.status == 'success') {

          this.status = response.status;
          this.message = response.message;
          console.log(response);
          form.reset();

        } else {

          this.status = 'error';
          this.message = response.message;

        }

      },
      error => {

        console.log(<any>error);

      }
    );
  }

}
