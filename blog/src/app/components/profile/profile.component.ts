import { Component, OnInit } from '@angular/core';
import { Post } from '../../models/post';
import { User } from '../../models/user';
import { PostService } from '../../services/post.service';
import { UserService } from '../../services/user.service'
import { Global } from '../../services/global';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [PostService, UserService]
})
export class ProfileComponent implements OnInit {

  public url;
  public posts: Array<Post>;
  public user: User;
  public status;
  public identity;
  public token;

  constructor(
  	private _postService: PostService,
  	private _userService: UserService,
  	private _route: ActivatedRoute,
  	private _router: Router
  ) {

    this.url = Global.url;
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }

  ngOnInit() {
  	this.getProfile();
  }

  getProfile() {

  	this._route.params.subscribe(params => {
  		let id = +params['id'];
  		this.getUser(id);
  		this.getPosts(id);
  	});

  }

  getUser(id) {
  	this._userService.getUser(id).subscribe(

  		response => {
  			if(response.status == 'success'){
  				this.user = response.user;
  				console.log(this.user);
  			}
  		},
  		error => {
  			console.log(<any>error)
  		}

  	);
  }

  getPosts(id) {

  	this._userService.getPosts(id).subscribe(

  		response => {
  			if(response.status == 'Success'){
  				this.posts = response.posts;
  			}
  		},
  		error => {
  			console.log(<any>error)
  		}

  	);

  }

  deletePost(id) {

    this._postService.delete(this.token, id).subscribe(

      response => {
        this._route.params.subscribe(params => {
	  		this.getProfile();
	  	});
      }, error => {
        console.log(<any>error);
      }

    );

  }

}

