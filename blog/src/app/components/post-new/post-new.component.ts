import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { PostService } from '../../services/post.service';
import { Post } from '../../models/post';
import { Global } from '../../services/global';

 
@Component({
  selector: 'app-post-new',
  templateUrl: './post-new.component.html',
  styleUrls: ['./post-new.component.css'],
  providers: [UserService, CategoryService, PostService]
})
export class PostNewComponent implements OnInit {

	public page_title: string;
	public identity;
	public token;
	public post: Post;
	public categories;
	public status;

  public froala_options: Object = {
    charCounterCount: true,
    languaje: 'es',
  }

	public afuConfig = {
	    multiple: false,
	    formatsAllowed: ".jpg, .png, .gif, .jpeg",
	    maxSize: "1",
	    uploadAPI:  {
	      url: Global.url+'post/upload',
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
  		private _route: ActivatedRoute,
  		private _router: Router,
  		private _userService: UserService,
  		private _categoryService: CategoryService,
  		private _postService: PostService
  	) { 

  		this.page_title = "Create a post";
  		this.identity = this._userService.getIdentity();
  		this.token = this._userService.getToken();
  	}

  	ngOnInit() {

  		this.post = new Post(1, this.identity.sub, 1, '', '', '', '');
  		this.getCategories();
  		//console.log(this.post);

  	}

  	getCategories() {

  		this._categoryService.getCategories().subscribe(
  			response => {

  				if(response.status == 'Success') {
  					this.categories = response.categories
  				}

  			},
  			error => {
  				console.log(<any>error);
  			}
  		);

  	}

  	imageUpload(e) {

  		let data = JSON.parse(e.response);

  		this.post.image = data.image;

  	}

  	onSubmit(form) {
  		
  		this._postService.create(this.token, this.post).subscribe(

  			response => {

  				if(response.status == 'Success') {
  					this.post = response.post;
  					this.status = 'Success';
  					form.reset();

  				} else {
  					
  					this.status = 'Error';
  				
  				}

  			},
  			error => {
  				
  				console.log(<any>error);
  				this.status = 'Error';
  			
  			}

  		);

  	}

}
